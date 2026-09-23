<?php

namespace App\Console\Commands;

use App\Services\LicenseService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateLicenseTokenCommand extends Command
{
    protected $signature = 'license:generate-token 
                            {--customer= : Nama supplier / customer}
                            {--devices=1 : Batas jumlah perangkat}
                            {--days= : Masa berlaku dalam hari (kosongkan untuk selamanya)}';

    protected $description = 'Menerbitkan token lisensi resmi baru untuk Supplier SPPG';

    public function handle(LicenseService $licenseService): int
    {
        $customer = $this->option('customer') ?: 'Supplier SPPG Mitra';
        $devices = (int) ($this->option('devices') ?: 1);
        $days = $this->option('days') ? (int) $this->option('days') : null;

        $token = LicenseService::generateTokenString();
        $expiresAt = $days ? Carbon::now()->addDays($days)->toIso8601String() : null;

        $payload = [
            'token' => $token,
            'business_name' => $customer,
            'max_devices' => $devices,
            'issued_at' => Carbon::now()->toIso8601String(),
            'expires_at' => $expiresAt,
            'plan' => 'standard_single_device',
        ];

        $signature = $licenseService->signPayload($payload);

        $this->info("==================================================");
        $this->info("       TOKEN LISENSI RESMI NUTRISAKA TERBIT       ");
        $this->info("==================================================");
        $this->line("<fg=yellow;options=bold>Token Lisensi  :</> <fg=green;options=bold>{$token}</>");
        $this->line("<fg=yellow>Supplier       :</> {$customer}");
        $this->line("<fg=yellow>Batas Perangkat:</> {$devices} Perangkat");
        $this->line("<fg=yellow>Masa Berlaku   :</> " . ($days ? "{$days} Hari (s/d {$expiresAt})" : "Permanen / Seumur Hidup"));
        $this->line("<fg=yellow>Digital Sign   :</> {$signature}");
        $this->info("==================================================");
        $this->comment("Berikan token ini kepada Supplier untuk dimasukkan pada halaman Registrasi Token di perangkatnya.");

        return Command::SUCCESS;
    }
}
