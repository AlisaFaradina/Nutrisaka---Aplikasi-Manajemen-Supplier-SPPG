<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseToken extends Model
{
    protected $fillable = [
        'token',
        'status',
        'device_fingerprint',
        'device_name',
        'supplier_name',
        'pic_name',
        'contact',
        'notes',
        'activated_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
    ];

    /**
     * Memeriksa apakah token masih belum terpakai (ready).
     */
    public function isUnused(): bool
    {
        return $this->status === 'unused';
    }

    /**
     * Memeriksa apakah token sedang aktif terikat pada perangkat.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Memeriksa apakah token telah dicabut oleh admin.
     */
    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    /**
     * Scope query untuk token belum terpakai.
     */
    public function scopeUnused($query)
    {
        return $query->where('status', 'unused');
    }

    /**
     * Scope query untuk token aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope query untuk token dicabut.
     */
    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    /**
     * Format ID perangkat ringkas untuk UI.
     */
    public function getShortFingerprintAttribute(): ?string
    {
        if (!$this->device_fingerprint) {
            return null;
        }
        $hash = strtoupper($this->device_fingerprint);
        return 'NDEV-' . substr($hash, 0, 4) . '-' . substr($hash, 4, 4);
    }
}
