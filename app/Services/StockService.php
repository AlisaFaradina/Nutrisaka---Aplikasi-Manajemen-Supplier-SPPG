<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    /**
     * Catat penambahan stok (Barang Masuk / Pembelian dari Supplier Upstream / Panen).
     */
    public function recordStockIn(Product $product, float $quantity, string $source = 'purchase', ?string $notes = null): StockMovement
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Kuantitas stok masuk harus lebih dari 0.');
        }

        return DB::transaction(function () use ($product, $quantity, $source, $notes) {
            $product->refresh();
            $beforeStock = (float) $product->current_stock;
            $afterStock = $beforeStock + $quantity;

            $product->update(['current_stock' => $afterStock]);

            return StockMovement::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $quantity,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'reference_type' => $source,
                'reference_id' => null,
                'notes' => $notes ?? 'Penerimaan barang masuk / pasokan',
            ]);
        });
    }

    /**
     * Catat pengurangan stok saat penjualan selesai (Dispatched / Sold).
     */
    public function recordSaleDeduction(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $product = $item->product;
                if (!$product) {
                    continue;
                }

                $beforeStock = (float) $product->current_stock;
                $deductQty = (float) $item->quantity;
                $afterStock = $beforeStock - $deductQty;

                $product->update(['current_stock' => $afterStock]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => $deductQty,
                    'before_stock' => $beforeStock,
                    'after_stock' => $afterStock,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'notes' => "Penjualan Faktur {$sale->invoice_number} ke SPPG {$sale->sppg->name}",
                ]);
            }
        });
    }

    /**
     * Kembalikan stok jika transaksi penjualan dibatalkan (Rollback / Retur).
     */
    public function rollbackSaleDeduction(Sale $sale, ?string $reason = null): void
    {
        DB::transaction(function () use ($sale, $reason) {
            foreach ($sale->items as $item) {
                $product = $item->product;
                if (!$product) {
                    continue;
                }

                $beforeStock = (float) $product->current_stock;
                $returnQty = (float) $item->quantity;
                $afterStock = $beforeStock + $returnQty;

                $product->update(['current_stock' => $afterStock]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $returnQty,
                    'before_stock' => $beforeStock,
                    'after_stock' => $afterStock,
                    'reference_type' => 'sale_cancellation',
                    'reference_id' => $sale->id,
                    'notes' => "Pembatalan Penjualan {$sale->invoice_number}. Alasan: " . ($reason ?? 'Pembatalan transaksi'),
                ]);
            }
        });
    }

    /**
     * Penyesuaian stok fisik (Stock Opname).
     */
    public function adjustStock(Product $product, float $physicalQty, string $notes): StockMovement
    {
        return DB::transaction(function () use ($product, $physicalQty, $notes) {
            $product->refresh();
            $beforeStock = (float) $product->current_stock;
            $afterStock = $physicalQty;
            $diff = $afterStock - $beforeStock;

            $product->update(['current_stock' => $afterStock]);

            return StockMovement::create([
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity' => abs($diff),
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'reference_type' => 'adjustment',
                'reference_id' => null,
                'notes' => $notes . " (Selisih: " . ($diff >= 0 ? "+{$diff}" : "{$diff}") . ")",
            ]);
        });
    }
}
