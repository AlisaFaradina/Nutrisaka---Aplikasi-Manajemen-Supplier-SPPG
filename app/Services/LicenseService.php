<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\License;
use App\Models\LicenseToken;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    /**
     * Kunci rahasia internal / public key fallback untuk verifikasi tanda tangan token.
     */
    protected const DEFAULT_SIGN_KEY = 'NUTRISAKA-OFFLINE-LICENSE-VERIFIER-KEY-2026';

    /**
     * Menghasilkan Device Fingerprint unik dan stabil untuk perangkat ini.
     */
    public function getDeviceFingerprint(): string
    {
        $components = [];

        // 1. Windows Machine GUID jika tersedia melalui registry
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            try {
                $guid = @exec('reg query "HKLM\SOFTWARE\Microsoft\Cryptography" /v MachineGuid 2>NUL');
                if ($guid && preg_match('/MachineGuid\s+REG_SZ\s+([a-zA-Z0-9\-]+)/i', $guid, $matches)) {
                    $components[] = trim($matches[1]);
                }
            } catch (\Throwable $e) {
                // Ignore fallback
            }
        }

        // 2. Hostname & OS uname
        $components[] = php_uname('s'); // OS name
        $components[] = php_uname('n'); // Hostname
        $components[] = php_uname('m'); // Machine architecture
        $components[] = gethostname() ?: 'UNKNOWN-HOST';

        // 3. User profil & environment info
        $components[] = getenv('COMPUTERNAME') ?: getenv('HOSTNAME') ?: 'NUTRI-DEVICE';

        return hash('sha256', implode('|#|', $components));
    }

    /**
     * Menghasilkan representasi ringkas fingerprint untuk ditampilkan di antarmuka (misal: NDEV-8A2F-9C4B).
     */
    public function getShortFingerprint(): string
    {
        $hash = strtoupper($this->getDeviceFingerprint());
        return 'NDEV-' . substr($hash, 0, 4) . '-' . substr($hash, 4, 4);
    }

    /**
     * Menyamarkan token untuk keamanan tampilan (misal: NTRS-••••-••••-4PLD).
     */
    public function maskToken(string $token): string
    {
        $clean = strtoupper(trim($token));
        $parts = explode('-', $clean);

        if (count($parts) >= 4) {
            return $parts[0] . '-••••-••••-' . end($parts);
        }

        if (strlen($clean) >= 8) {
            return substr($clean, 0, 4) . '••••' . substr($clean, -4);
        }

        return 'NTRS-••••-••••-VALID';
    }

    /**
     * Memeriksa apakah perangkat ini sudah terdaftar lisensinya.
     */
    public function isRegistered(): bool
    {
        $license = License::first();
        return $license !== null && $license->status !== 'locked';
    }

    /**
     * Verifikasi lisensi secara lokal (offline-first & lifetime).
     * Lisensi berlaku selamanya untuk perangkat ini kecuali:
     * 1. Belum terdaftar (unlicensed)
     * 2. Perangkat fisik berbeda (device mismatch)
     * 3. Dicabut oleh Admin (revoked)
     * 4. Integritas tanda tangan rusak
     */
    public function verifyLocal(): array
    {
        $license = License::first();

        if (!$license) {
            return [
                'valid' => false,
                'status' => 'unlicensed',
                'message' => 'Aplikasi belum terdaftar. Silakan masukkan token lisensi resmi Anda.',
                'license' => null,
            ];
        }

        $currentFingerprint = $this->getDeviceFingerprint();

        // 1. Cek kecocokan sidik perangkat (mencegah database disalin ke perangkat lain)
        if ($license->device_fingerprint !== $currentFingerprint) {
            return [
                'valid' => false,
                'status' => 'locked',
                'message' => 'Perangkat Berbeda Terdeteksi! Lisensi ini terikat pada perangkat fisik lain. Setiap perangkat (komputer desktop, android, dll) membutuhkan token lisensi tersendiri. Silakan hubungi sakanutri@gmail.com.',
                'license' => $license,
            ];
        }

        // 2. Verifikasi tanda tangan digital payload
        if (!$this->verifySignature($license->license_payload, $license->signature)) {
            return [
                'valid' => false,
                'status' => 'locked',
                'message' => 'Integritas data lisensi tidak valid atau telah dimodifikasi.',
                'license' => $license,
            ];
        }

        // 3. Cek apakah token telah dicabut oleh Administrator di tabel license_tokens
        $tokenRecord = LicenseToken::where('device_fingerprint', $currentFingerprint)->first();
        if ($tokenRecord && $tokenRecord->isRevoked()) {
            if ($license->status !== 'locked') {
                $license->update(['status' => 'locked']);
            }
            return [
                'valid' => false,
                'status' => 'locked',
                'message' => 'Lisensi ini telah dicabut atau dinonaktifkan oleh Administrator Nutrisaka. Hubungi sakanutri@gmail.com.',
                'license' => $license,
            ];
        }

        // Lisensi aktif selamanya (permanen)
        if ($license->status !== 'active') {
            $license->update(['status' => 'active']);
        }

        return [
            'valid' => true,
            'status' => 'active',
            'message' => 'Lisensi aktif selamanya untuk perangkat ini.',
            'license' => $license,
        ];
    }

    /**
     * Mendaftarkan dan mengaktivasi token lisensi di perangkat ini.
     */
    public function activate(string $token, array $supplierData): array
    {
        $token = strtoupper(trim($token));

        if (empty($token)) {
            throw new Exception('Token lisensi wajib diisi.');
        }

        // Validasi format token (contoh: NTRS-XXXX-XXXX-XXXX atau NTRS-DEMO-2026-DEV1)
        if (!preg_match('/^NTRS-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $token) &&
            !preg_match('/^NTRS-DEMO-[A-Z0-9\-]+$/', $token)) {
            throw new Exception('Format token lisensi tidak valid. Format harus seperti: NTRS-7K2M-Q9XA-4PLD.');
        }

        $fingerprint = $this->getDeviceFingerprint();
        $serverUrl = config('services.license_server.url') ?: env('LICENSE_SERVER_URL');

        $isDemo = (app()->environment('local', 'testing') && str_starts_with($token, 'NTRS-DEMO'));

        // 1. Validasi ke tabel license_tokens lokal
        $tokenRecord = LicenseToken::where('token', $token)->first();

        if ($tokenRecord) {
            if ($tokenRecord->isRevoked()) {
                throw new Exception('Token lisensi ini telah dicabut oleh Administrator Nutrisaka. Hubungi sakanutri@gmail.com untuk meminta token baru.');
            }

            if ($tokenRecord->isActive()) {
                // Jika sudah aktif pada perangkat fisik yang berbeda
                if ($tokenRecord->device_fingerprint && $tokenRecord->device_fingerprint !== $fingerprint) {
                    $devName = $tokenRecord->device_name ?: 'Perangkat Lain';
                    throw new Exception("Token ini sudah terikat dan digunakan pada {$devName}. Satu token hanya berlaku untuk satu perangkat fisik. Jika ingin menggunakan pada perangkat ini, silakan ajukan token baru ke sakanutri@gmail.com.");
                }
            }

            // Kunci token ke sidik perangkat ini
            $tokenRecord->update([
                'status' => 'active',
                'device_fingerprint' => $fingerprint,
                'device_name' => php_uname('s') . ' - ' . (gethostname() ?: 'Perangkat') . ' (' . (php_uname('m') ?: 'x64') . ')',
                'supplier_name' => $supplierData['supplier_name'] ?? $tokenRecord->supplier_name,
                'pic_name' => $supplierData['pic_name'] ?? $tokenRecord->pic_name,
                'contact' => $supplierData['supplier_phone'] ?? $tokenRecord->contact,
                'activated_at' => Carbon::now(),
            ]);
        } else {
            // Jika token belum ada di tabel lokal (misal token resmi baru yang di-generate offline oleh admin),
            // daftarkan token ini ke tabel license_tokens
            LicenseToken::create([
                'token' => $token,
                'status' => 'active',
                'device_fingerprint' => $fingerprint,
                'device_name' => php_uname('s') . ' - ' . (gethostname() ?: 'Perangkat') . ' (' . (php_uname('m') ?: 'x64') . ')',
                'supplier_name' => $supplierData['supplier_name'] ?? 'Supplier SPPG',
                'pic_name' => $supplierData['pic_name'] ?? '',
                'contact' => $supplierData['supplier_phone'] ?? '',
                'notes' => 'Aktivasi langsung di perangkat',
                'activated_at' => Carbon::now(),
            ]);
        }

        // 2. Jika URL License Server dikonfigurasi, hubungi server
        if ($serverUrl && !$isDemo) {
            try {
                $response = Http::timeout(10)->post(rtrim($serverUrl, '/') . '/api/activate', [
                    'token' => $token,
                    'device_fingerprint' => $fingerprint,
                    'device_name' => gethostname(),
                    'business_name' => $supplierData['supplier_name'] ?? '',
                    'contact' => $supplierData['supplier_phone'] ?? '',
                    'app_version' => '1.0.1',
                ]);

                if ($response->failed()) {
                    $errorMsg = $response->json('message') ?? 'Aktivasi ditolak oleh Server Lisensi. Pastikan token belum digunakan di perangkat lain.';
                    throw new Exception($errorMsg);
                }

                $responseData = $response->json();
                $payload = $responseData['payload'] ?? [];
                $signature = $responseData['signature'] ?? '';
            } catch (Exception $e) {
                throw $e;
            } catch (\Throwable $e) {
                throw new Exception('Gagal menghubungi Server Lisensi: ' . $e->getMessage());
            }
        } else {
            // Mode Offline / Lokal: HMAC Verification
            $payload = [
                'token' => $token,
                'device_fingerprint' => $fingerprint,
                'business_name' => $supplierData['supplier_name'] ?? 'Supplier SPPG',
                'pic_name' => $supplierData['pic_name'] ?? '',
                'issued_at' => Carbon::now()->toIso8601String(),
                'expires_at' => null, // Lifetime selamanya
                'plan' => 'standard_single_device',
                'max_devices' => 1,
            ];
            $signature = $this->signPayload($payload);
        }

        $payloadJson = json_encode($payload);

        // 3. Simpan lisensi lokal di database — PERMANEN (grace_until = null)
        $license = License::first();
        if ($license) {
            $license->update([
                'token_masked' => $this->maskToken($token),
                'license_payload' => $payloadJson,
                'signature' => $signature,
                'device_fingerprint' => $fingerprint,
                'status' => 'active',
                'activated_at' => Carbon::now(),
                'last_validated_at' => Carbon::now(),
                'grace_until' => null, // Selamanya
            ]);
        } else {
            $license = License::create([
                'token_masked' => $this->maskToken($token),
                'license_payload' => $payloadJson,
                'signature' => $signature,
                'device_fingerprint' => $fingerprint,
                'status' => 'active',
                'activated_at' => Carbon::now(),
                'last_validated_at' => Carbon::now(),
                'grace_until' => null, // Selamanya
            ]);
        }

        // Update profil usaha supplier di app_settings
        if (!empty($supplierData['supplier_name'])) {
            AppSetting::set('supplier_name', $supplierData['supplier_name']);
        }
        if (!empty($supplierData['pic_name'])) {
            AppSetting::set('supplier_pic', $supplierData['pic_name']);
        }
        if (!empty($supplierData['supplier_phone'])) {
            AppSetting::set('supplier_phone', $supplierData['supplier_phone']);
        }
        if (!empty($supplierData['supplier_address'])) {
            AppSetting::set('supplier_address', $supplierData['supplier_address']);
        }
        if (!empty($supplierData['pin'])) {
            AppSetting::set('security_pin', $supplierData['pin']);
        }

        return [
            'success' => true,
            'message' => 'Registrasi token lisensi berhasil! Aplikasi aktif selamanya pada perangkat ini.',
            'license' => $license,
        ];
    }

    /**
     * Memvalidasi ulang lisensi (sinkronisasi status).
     */
    public function revalidateOnline(): array
    {
        $license = License::first();
        if (!$license) {
            return ['success' => false, 'message' => 'Aplikasi belum memiliki lisensi.'];
        }

        $fingerprint = $this->getDeviceFingerprint();

        // Cek apakah token dicabut oleh admin di database lokal
        $tokenRecord = LicenseToken::where('device_fingerprint', $fingerprint)->first();
        if ($tokenRecord && $tokenRecord->isRevoked()) {
            $license->update(['status' => 'locked']);
            return ['success' => false, 'message' => 'Lisensi ini telah dicabut oleh Administrator Nutrisaka.'];
        }

        $serverUrl = config('services.license_server.url') ?: env('LICENSE_SERVER_URL');

        if ($serverUrl) {
            try {
                $response = Http::timeout(8)->post(rtrim($serverUrl, '/') . '/api/validate', [
                    'device_fingerprint' => $fingerprint,
                    'token_masked' => $license->token_masked,
                ]);

                if ($response->successful()) {
                    $license->update([
                        'status' => 'active',
                        'last_validated_at' => Carbon::now(),
                        'grace_until' => null,
                    ]);

                    return ['success' => true, 'message' => 'Status lisensi berhasil divalidasi.'];
                }

                if ($response->status() === 403 || $response->json('status') === 'revoked') {
                    $license->update(['status' => 'locked']);
                    return ['success' => false, 'message' => 'Lisensi telah dicabut atau dinonaktifkan oleh Administrator.'];
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal revalidasi online lisensi: ' . $e->getMessage());
            }
        }

        // Pengecekan integritas lokal
        $verification = $this->verifyLocal();
        if ($verification['valid']) {
            $license->update([
                'status' => 'active',
                'last_validated_at' => Carbon::now(),
                'grace_until' => null,
            ]);
            return ['success' => true, 'message' => 'Status lisensi sah dan aktif selamanya untuk perangkat ini.'];
        }

        return ['success' => false, 'message' => $verification['message']];
    }

    /**
     * Menonaktifkan perangkat ini (logout / lepas lisensi).
     * Token akan dilepas dari perangkat ini sehingga bisa digunakan kembali.
     */
    public function deactivate(): bool
    {
        $license = License::first();
        if (!$license) {
            return true;
        }

        $fingerprint = $this->getDeviceFingerprint();

        // Lepas binding perangkat pada tabel license_tokens
        $tokenRecord = LicenseToken::where('device_fingerprint', $fingerprint)->first();
        if ($tokenRecord) {
            $tokenRecord->update([
                'status' => 'unused',
                'device_fingerprint' => null,
                'device_name' => null,
                'notes' => ($tokenRecord->notes ? $tokenRecord->notes . ' | ' : '') . 'Perangkat dilepas/logout pada ' . Carbon::now()->format('d/m/Y H:i'),
            ]);
        }

        $serverUrl = config('services.license_server.url') ?: env('LICENSE_SERVER_URL');
        if ($serverUrl) {
            try {
                Http::timeout(6)->post(rtrim($serverUrl, '/') . '/api/deactivate', [
                    'device_fingerprint' => $fingerprint,
                    'token_masked' => $license->token_masked,
                ]);
            } catch (\Throwable $e) {
                // Ignore error
            }
        }

        $license->delete();
        return true;
    }

    /**
     * Menandatangani payload secara kriptografis (HMAC-SHA256).
     */
    public function signPayload(array $payload, ?string $key = null): string
    {
        $key = $key ?: (env('LICENSE_SIGNING_KEY') ?: self::DEFAULT_SIGN_KEY);
        ksort($payload);
        $dataToSign = json_encode($payload, JSON_UNESCAPED_SLASHES);
        return hash_hmac('sha256', $dataToSign, $key);
    }

    /**
     * Memverifikasi keabsahan tanda tangan kriptografis.
     */
    public function verifySignature(string $payloadJson, string $signature): bool
    {
        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            return false;
        }

        $expected = $this->signPayload($payload);
        return hash_equals($expected, $signature);
    }

    /**
     * Helper untuk membuat token acak resmi yang mudah dibaca (misal: NTRS-7K2M-Q9XA-4PLD).
     */
    public static function generateTokenString(): string
    {
        // Karakter tanpa huruf ambigu (tanpa 0, O, 1, I)
        $charset = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $blocks = [];

        for ($b = 0; $b < 3; $b++) {
            $block = '';
            for ($i = 0; $i < 4; $i++) {
                $block .= $charset[random_int(0, strlen($charset) - 1)];
            }
            $blocks[] = $block;
        }

        return 'NTRS-' . implode('-', $blocks);
    }
}
