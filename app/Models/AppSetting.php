<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getAllSettings(): array
    {
        $defaults = [
            'supplier_name' => 'Nutrisaka Supplier Bahan Pangan & Gizi',
            'supplier_tagline' => 'Mitra Terpercaya Kebutuhan Bahan Pangan & Nutrisi SPPG',
            'supplier_phone' => '0812-3456-7890',
            'supplier_email' => 'supplier.nutrisaka@gmail.com',
            'supplier_address' => 'Jl. Pangan Sejahtera No. 88, Kawasan Logistik Pangan Nusantara',
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '137-00-9876543-2',
            'bank_account_name' => 'NUTRISAKA SUPPLIER PANGAN',
            'invoice_footer_notes' => 'Barang yang telah diterima harap diperiksa. Pembayaran transfer mohon sertakan No. Invoice.',
            'security_pin' => '1234',
            'thermal_paper_size' => '58mm',
        ];

        $stored = static::pluck('value', 'key')->toArray();
        return array_merge($defaults, $stored);
    }
}
