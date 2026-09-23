<?php

namespace App\Http\Controllers;

use App\Services\LicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    /**
     * Memeriksa ulang status lisensi ke server online secara manual.
     */ 
    public function checkOnline()
    {
        $result = $this->licenseService->revalidateOnline();

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Menonaktifkan perangkat ini (melepas lisensi) agar token dapat digunakan di perangkat lain.
     */
    public function deactivate(Request $request)
    {
        $this->licenseService->deactivate();

        return redirect()->route('activation.register')
            ->with('info', 'Perangkat ini telah dinonaktifkan. Lisensi berhasil dilepas dan dapat didaftarkan kembali.');
    }
}
