<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Services\BackupService;
use Database\Seeders\NutrisakaSampleSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Services\LicenseService;

class SettingController extends Controller
{
    public function __construct(
        protected BackupService $backupService,
        protected LicenseService $licenseService
    ) {}

    public function index()
    {
        $settings = AppSetting::getAllSettings();
        $license = \App\Models\License::first();
        $shortFingerprint = $this->licenseService->getShortFingerprint();
        return view('settings.index', compact('settings', 'license', 'shortFingerprint'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_tagline' => 'nullable|string|max:255',
            'supplier_phone' => 'nullable|string|max:50',
            'supplier_email' => 'nullable|email|max:100',
            'supplier_address' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:100',
            'invoice_footer_notes' => 'nullable|string',
            'thermal_paper_size' => 'required|in:58mm,80mm',
        ]);

        foreach ($validated as $key => $value) {
            AppSetting::set($key, $value);
        }

        return back()->with('success', 'Profil Supplier berhasil diperbarui.');
    }

    public function updatePin(Request $request)
    {
        $validated = $request->validate([
            'current_pin' => 'nullable|string',
            'new_pin' => 'required|string|min:4|max:6',
        ]);

        $currentStored = AppSetting::get('security_pin', '1234');
        if ($currentStored && $currentStored !== $validated['current_pin']) {
            return back()->with('error', 'PIN lama salah.');
        }

        AppSetting::set('security_pin', $validated['new_pin']);

        return back()->with('success', 'PIN Keamanan berhasil diperbarui.');
    }

    public function exportBackup(): StreamedResponse
    {
        $data = $this->backupService->exportBackup();
        $filename = 'nutrisaka_backup_' . now()->format('Ymd_His') . '.json';

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function importRestore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json,txt',
        ]);

        try {
            $content = file_get_contents($request->file('backup_file')->getRealPath());
            $payload = json_decode($content, true);

            if (!$payload) {
                return back()->with('error', 'File JSON tidak valid atau rusak.');
            }

            $this->backupService->restoreBackup($payload);

            return back()->with('success', 'Data aplikasi berhasil dipulihkan secara utuh dari file cadangan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }
    }

    public function loadDemoData(Request $request)
    {
        try {
            Artisan::call('db:seed', [
                '--class' => 'NutrisakaSampleSeeder',
                '--force' => true,
            ]);

            return redirect()->route('dashboard')->with('success', 'Data simulasi contoh (SPPG, produk, pesanan, penjualan, stok) berhasil dimuat!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat data contoh: ' . $e->getMessage());
        }
    }
}
