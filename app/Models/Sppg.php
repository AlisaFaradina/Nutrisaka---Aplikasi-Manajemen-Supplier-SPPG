<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sppg extends Model
{
    protected $fillable = [
        'code',
        'name',
        'pic_name',
        'phone',
        'address',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalSalesAmountAttribute(): float
    {
        return (float) $this->sales()->where('status', 'selesai')->sum('total_amount');
    }

    public function getTotalPaidAmountAttribute(): float
    {
        return (float) $this->sales()->where('status', 'selesai')->sum('paid_amount');
    }

    public function getTotalDebtAttribute(): float
    {
        return (float) $this->sales()->where('status', 'selesai')->sum('remaining_balance');
    }

    public static function generateCode(): string
    {
        $count = static::count() + 1;
        return 'SPPG-' . str_pad((string)$count, 3, '0', STR_PAD_LEFT);
    }
}
