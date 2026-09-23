<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'invoice_number',
        'order_id',
        'sppg_id',
        'sale_date',
        'due_date',
        'subtotal',
        'discount',
        'total_amount',
        'paid_amount',
        'remaining_balance',
        'payment_status',
        'status',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'float',
        'discount' => 'float',
        'total_amount' => 'float',
        'paid_amount' => 'float',
        'remaining_balance' => 'float',
    ];

    public function sppg(): BelongsTo
    {
        return $this->belongsTo(Sppg::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('payment_date');
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'lunas' => 'Lunas',
            'sebagian' => 'Sebagian (Piutang)',
            'belum_bayar' => 'Belum Bayar',
            default => ucfirst($this->payment_status),
        };
    }

    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "INV-{$date}-";
        $last = static::where('invoice_number', 'like', "{$prefix}%")->latest('id')->first();
        if ($last) {
            $num = (int) substr($last->invoice_number, strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }
}
