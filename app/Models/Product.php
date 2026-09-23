<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'unit',
        'selling_price',
        'cost_price',
        'current_stock',
        'min_stock',
        'is_active',
        'description',
    ];

    protected $casts = [
        'selling_price' => 'float',
        'cost_price' => 'float',
        'current_stock' => 'float',
        'min_stock' => 'float',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'habis';
        }
        if ($this->current_stock <= $this->min_stock) {
            return 'menipis';
        }
        return 'aman';
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'habis' => 'Stok Habis',
            'menipis' => 'Stok Menipis',
            'aman' => 'Stok Aman',
        };
    }

    public static function generateSku(): string
    {
        $count = static::count() + 1;
        return 'PRD-' . str_pad((string)$count, 3, '0', STR_PAD_LEFT);
    }
}
