<?php

namespace App\Console\Commands;

use App\Services\LicenseService;
use Illuminate\Console\Command;

class CheckLicenseCommand extends Command
{
    protected $signature = 'license:check {--online : Jalankan validasi online ke server}';

    protected $description = 'Memeriksa status lisensi lokal perangkat dan masa tenggang offline';

    public function handle(LicenseService $licenseService): int
    {
        $this->info("Memeriksa status lisensi perangkat...");

        if ($this->option('online')) {
            $this->comment("Menghubungi server lisensi untuk revalidasi online...");
            $result = $licenseService->revalidateOnline();
            if ($result['success']) {
                $this->info("✓ " . $result['message']);
            } else {
                $this->error("✗ " . $result['message']);
            }
        }

        $verification = $licenseService->verifyLocal();

        $this->table(['Parameter', 'Nilai'], [
            ['Status Valid', $verification['valid'] ? 'YA (Aktif)' : 'TIDAK'],
            ['Status Lisensi', strtoupper($verification['status'])],
            ['Pesan', $verification['message']],
            ['Sidik Perangkat', $licenseService->getShortFingerprint()],
            ['Token', $verification['license'] ? $verification['license']->token_masked : 'Belum Terdaftar'],
            ['Masa Tenggang Sisa', $verification['license'] ? ($verification['license']->daysUntilLock() . ' hari') : '-'],
        ]);

        return $verification['valid'] ? Command::SUCCESS : Command::FAILURE;
    }
}
