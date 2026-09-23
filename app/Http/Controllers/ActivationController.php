<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\License;
use App\Services\LicenseService;
use Exception;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    /**
     * Menampilkan Halaman Registrasi Token & Data Usaha Supplier.
     */
    public function showRegister()
    {
        $verification = $this->licenseService->verifyLocal();
        if ($verification['valid']) {
            return redirect()->route('dashboard')
                ->with('info', 'Perangkat ini telah memiliki lisensi aktif.');
        }

        $fingerprint = $this->licenseService->getDeviceFingerprint();
        $shortFingerprint = $this->licenseService->getShortFingerprint();
        $settings = AppSetting::getAllSettings();

        $isLocal = app()->environment('local', 'testing');

        return view('activation.register', compact(
            'fingerprint',
            'shortFingerprint',
            'settings',
            'isLocal'
        ));
    }

    /**
     * Memproses pendaftaran token dan inisialisasi profil usaha supplier.
     */
    public function register(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', 'max:50'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'pic_name' => ['required', 'string', 'max:255'],
            'supplier_phone' => ['required', 'string', 'max:50'],
            'supplier_address' => ['nullable', 'string', 'max:500'],
            'pin' => ['required', 'string', 'min:4', 'max:6'],
        ], [
            'token.required' => 'Token lisensi resmi wajib diisi.',
            'supplier_name.required' => 'Nama usaha / perusahaan supplier wajib diisi.',
            'pic_name.required' => 'Nama penanggung jawab (PIC) wajib diisi.',
            'supplier_phone.required' => 'Nomor telepon / WhatsApp aktif wajib diisi.',
            'pin.required' => 'PIN keamanan lokal 4-6 angka wajib dibuat.',
            'pin.min' => 'PIN keamanan minimal terdiri dari 4 angka.',
            'pin.max' => 'PIN keamanan maksimal terdiri dari 6 angka.',
        ]);

        try {
            $this->licenseService->activate(
                $request->input('token'),
                $request->only([
                    'supplier_name',
                    'pic_name',
                    'supplier_phone',
                    'supplier_address',
                    'pin',
                ])
            );

            return redirect()->route('dashboard')
                ->with('success', 'Selamat Datang di Nutrisaka! Registrasi lisensi perangkat berhasil diaktivasi dan data usaha Anda telah tersimpan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Registrasi instan menggunakan token demo untuk lingkungan lokal / demo.
     */
    public function registerDemo()
    {
        if (!app()->environment('local', 'testing')) {
            abort(403, 'Aksi demo hanya diizinkan pada lingkungan pengembangan.');
        }

        try {
            $demoToken = 'NTRS-DEMO-2026-DEV1';
            $this->licenseService->activate($demoToken, [
                'supplier_name' => 'CV Nutrisaka Pangan Mandiri (Demo)',
                'pic_name' => 'Ahmad Fauzi',
                'supplier_phone' => '0812-3456-7890',
                'supplier_address' => 'Kawasan Logistik Pergudangan SPPG No. 12, Jakarta',
                'pin' => '1234',
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Mode Demo Aktif: Lisensi pengembangan dan data usaha awal berhasil diaktivasi.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal mengaktifkan token demo: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan layar terkunci ramah awam jika lisensi berakhir atau dicabut.
     */
    public function showLocked()
    {
        $license = License::first();
        $fingerprint = $this->licenseService->getDeviceFingerprint();
        $shortFingerprint = $this->licenseService->getShortFingerprint();

        return view('activation.locked', compact('license', 'fingerprint', 'shortFingerprint'));
    }

    /**
     * Validasi ulang status lisensi ke server online saat di layar terkunci.
     */
    public function revalidate()
    {
        $result = $this->licenseService->revalidateOnline();

        if ($result['success']) {
            return redirect()->route('dashboard')
                ->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
