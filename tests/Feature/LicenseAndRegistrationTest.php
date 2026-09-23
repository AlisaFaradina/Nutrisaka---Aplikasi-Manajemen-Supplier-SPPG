<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\License;
use App\Services\BackupService;
use App\Services\LicenseService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseAndRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected LicenseService $licenseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->licenseService = app(LicenseService::class);
    }

    public function test_unregistered_device_is_redirected_to_register_page(): void
    {
        // Tanpa lisensi, route dashboard harus mengarahkan ke halaman register-token
        $response = $this->get('/dashboard');
        $response->assertRedirect('/register-token');

        $responseOrders = $this->get('/orders');
        $responseOrders->assertRedirect('/register-token');
    }

    public function test_registration_requires_mandatory_fields(): void
    {
        $response = $this->post('/register-token', []);
        $response->assertSessionHasErrors([
            'token',
            'supplier_name',
            'pic_name',
            'supplier_phone',
            'pin',
        ]);
    }

    public function test_registration_fails_with_invalid_token_format(): void
    {
        $response = $this->post('/register-token', [
            'token' => 'SALAH-TOKEN-123',
            'supplier_name' => 'CV Sumber Rejeki',
            'pic_name' => 'Budi',
            'supplier_phone' => '0812345678',
            'supplier_address' => 'Jl. Pangan No. 1',
            'pin' => '1234',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseEmpty('licenses');
    }

    public function test_registration_succeeds_with_valid_token_and_sets_up_supplier_profile(): void
    {
        $validToken = 'NTRS-7K2M-Q9XA-4PLD';

        $response = $this->post('/register-token', [
            'token' => $validToken,
            'supplier_name' => 'CV Berkah Nutrisi SPPG',
            'pic_name' => 'Haji Mansyur',
            'supplier_phone' => '0812-9988-7766',
            'supplier_address' => 'Gudang Pusat Logistik SPPG Blok C',
            'pin' => '4321',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        // Verifikasi lisensi tersimpan di database
        $this->assertDatabaseHas('licenses', [
            'token_masked' => 'NTRS-••••-••••-4PLD',
            'status' => 'active',
            'device_fingerprint' => $this->licenseService->getDeviceFingerprint(),
        ]);

        // Verifikasi profil supplier tersimpan di app_settings
        $this->assertEquals('CV Berkah Nutrisi SPPG', AppSetting::get('supplier_name'));
        $this->assertEquals('Haji Mansyur', AppSetting::get('supplier_pic'));
        $this->assertEquals('0812-9988-7766', AppSetting::get('supplier_phone'));
        $this->assertEquals('4321', AppSetting::get('security_pin'));

        // Setelah terdaftar, akses dashboard sekarang berhasil (HTTP 200)
        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
    }

    public function test_demo_registration_works_in_local_environment(): void
    {
        $response = $this->post('/register-token/demo');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('licenses', 1);
        $license = License::first();
        $this->assertEquals('active', $license->status);
    }

    public function test_fingerprint_mismatch_locks_the_application(): void
    {
        // Simulasikan lisensi yang dibuat untuk perangkat lain (fingerprint berbeda)
        License::create([
            'token_masked' => 'NTRS-••••-••••-9999',
            'license_payload' => json_encode(['token' => 'NTRS-AAAA-BBBB-CCCC']),
            'signature' => 'dummy_signature',
            'device_fingerprint' => 'FINGERPRINT-PERANGKAT-LAIN-XYZ',
            'status' => 'active',
            'activated_at' => now(),
            'last_validated_at' => now(),
            'grace_until' => now()->addDays(30),
        ]);

        // Mencoba membuat pesanan baru (POST) harus ditolak dan dialihkan ke /terkunci
        $response = $this->post('/orders', [
            'sppg_id' => 1,
            'items' => [],
        ]);

        $response->assertRedirect('/terkunci');
    }

    public function test_locked_device_can_still_export_data_backup(): void
    {
        // Pasang lisensi berstatus locked
        License::create([
            'token_masked' => 'NTRS-••••-••••-1234',
            'license_payload' => json_encode(['token' => 'NTRS-1111-2222-3333']),
            'signature' => 'dummy_sign',
            'device_fingerprint' => $this->licenseService->getDeviceFingerprint(),
            'status' => 'locked',
            'activated_at' => now()->subDays(60),
            'last_validated_at' => now()->subDays(60),
            'grace_until' => now()->subDays(10), // Grace period sudah lewat
        ]);

        // Export backup tetap harus diizinkan (data tidak pernah disandera)
        $response = $this->get('/settings/export-backup');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
    }

    public function test_backup_export_strictly_excludes_licenses_table(): void
    {
        // Aktivasi lisensi
        $this->licenseService->activate('NTRS-TEST-VALI-0001', [
            'supplier_name' => 'Supplier Aman',
            'pic_name' => 'Budi',
            'supplier_phone' => '0812345678',
            'pin' => '1234',
        ]);

        $backupService = app(BackupService::class);
        $backupData = $backupService->exportBackup();

        // Pastikan licenses TIDAK dimasukkan dalam data backup JSON
        $this->assertArrayNotHasKey('licenses', $backupData['data']);
        $this->assertArrayHasKey('app_settings', $backupData['data']);
        $this->assertArrayHasKey('products', $backupData['data']);
    }

    public function test_deactivation_clears_license_and_redirects_to_registration(): void
    {
        // Aktivasi dulu
        $this->licenseService->activate('NTRS-AAAA-BBBB-CCCC', [
            'supplier_name' => 'Supplier Uji',
            'pic_name' => 'Uji',
            'supplier_phone' => '0812345678',
            'pin' => '1234',
        ]);

        $this->assertDatabaseCount('licenses', 1);

        // Lepas lisensi
        $response = $this->post('/settings/license/deactivate');
        $response->assertRedirect('/register-token');
        $this->assertDatabaseEmpty('licenses');
    }
}
