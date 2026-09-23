<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class License extends Model
{
    protected $fillable = [
        'token_masked',
        'license_payload',
        'signature',
        'public_key',
        'device_fingerprint',
        'status',
        'activated_at',
        'last_validated_at',
        'grace_until',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'last_validated_at' => 'datetime',
        'grace_until' => 'datetime',
    ];

    /**
     * Decode payload JSON menjadi array.
     */
    public function getPayloadAttribute(): array
    {
        if (empty($this->license_payload)) {
            return [];
        }

        $decoded = json_decode($this->license_payload, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Memeriksa apakah lisensi aktif dan dapat digunakan.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'grace'], true);
    }

    /**
     * Memeriksa apakah lisensi sedang dalam masa tenggang offline (grace period).
     * Lisensi Nutrisaka berlaku selamanya (lifetime), tidak ada grace period expiry.
     */
    public function isInGrace(): bool
    {
        return false;
    }

    /**
     * Memeriksa apakah lisensi terkunci (wajib cek ulang ke server).
     */
    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    /**
     * Menghitung sisa hari sebelum aplikasi terkunci.
     * Return null karena lisensi permanen seumur hidup perangkat.
     */
    public function daysUntilLock(): ?int
    {
        return null;
    }
}
