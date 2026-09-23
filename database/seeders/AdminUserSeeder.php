<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\LicenseToken;
use App\Services\LicenseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin / Pemilik Aplikasi Nutrisaka
        AdminUser::updateOrCreate(
            ['email' => 'sakanutri@gmail.com'],
            [
                'name' => 'Pemilik Nutrisaka (Admin)',
                'password' => Hash::make('admin123'),
                'role' => 'owner',
            ]
        );

        // 2. Buat beberapa token lisensi awal jika belum ada
        if (LicenseToken::count() === 0) {
            $initialTokens = [
                [
                    'token' => 'NTRS-7K2M-Q9XA-4PLD',
                    'status' => 'unused',
                    'notes' => 'Token Resmi Paket Starter SPPG',
                ],
                [
                    'token' => 'NTRS-9F3R-8HBW-2JKX',
                    'status' => 'unused',
                    'notes' => 'Token Resmi Perangkat Android / Tablet',
                ],
                [
                    'token' => 'NTRS-4X9L-6NTP-5CYM',
                    'status' => 'unused',
                    'notes' => 'Token Resmi Desktop Windows Kasir Logistik',
                ],
            ];

            foreach ($initialTokens as $data) {
                LicenseToken::create($data);
            }
        }
    }
}
