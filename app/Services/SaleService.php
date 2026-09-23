<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SaleService
{
    public function __construct(
        protected StockService $stockService,
        protected PaymentService $paymentService
    ) {}

    /**
     * Membuat penjualan baru (bisa dari pesanan atau penjualan langsung / kasir cepat).
     */
    public function createSale(array $saleData, array $items, ?Order $order = null): Sale
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Daftar barang penjualan tidak boleh kosong.');
        }

        return DB::transaction(function () use ($saleData, $items, $order) {
            $subtotal = 0;
            $preparedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (float) $item['quantity'];
                $price = isset($item['unit_price']) ? (float) $item['unit_price'] : (float) $product->selling_price;
                $itemSubtotal = $qty * $price;
                $subtotal += $itemSubtotal;

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discount = isset($saleData['discount']) ? (float) $saleData['discount'] : 0;
            $totalAmount = max(0, $subtotal - $discount);
            $initialPaid = isset($saleData['initial_paid']) ? (float) $saleData['initial_paid'] : 0;
            $remainingBalance = max(0, $totalAmount - $initialPaid);

            $paymentStatus = 'belum_bayar';
            if ($initialPaid >= $totalAmount && $totalAmount > 0) {
                $paymentStatus = 'lunas';
            } elseif ($initialPaid > 0) {
                $paymentStatus = 'sebagian';
            }

            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'order_id' => $order?->id,
                'sppg_id' => $saleData['sppg_id'],
                'sale_date' => $saleData['sale_date'] ?? now()->toDateString(),
                'due_date' => $saleData['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'paid_amount' => min($initialPaid, $totalAmount),
                'remaining_balance' => $remainingBalance,
                'payment_status' => $paymentStatus,
                'status' => 'selesai',
                'notes' => $saleData['notes'] ?? null,
            ]);

            foreach ($preparedItems as $prepItem) {
                $prepItem['sale_id'] = $sale->id;
                SaleItem::create($prepItem);
            }

            // Potong stok produk sesuai barang riil yang terjual
            $this->stockService->recordSaleDeduction($sale);

            // Jika ada pembayaran awal, catat ke tabel payments
            if ($initialPaid > 0) {
                $this->paymentService->recordPayment(
                    sale: $sale,
                    amount: min($initialPaid, $totalAmount),
                    method: $saleData['payment_method'] ?? 'tunai',
                    reference: $saleData['payment_reference'] ?? null,
                    notes: 'Pembayaran awal saat transaksi penjualan'
                );
            }

            // Jika dibuat dari pesanan, update status pesanan menjadi selesai
            if ($order) {
                $order->update(['status' => 'selesai']);
            }

            return $sale;
        });
    }

    /**
     * Batalkan penjualan dan kembalikan stok.
     */
    public function cancelSale(Sale $sale, ?string $reason = null): void
    {
        if ($sale->status === 'batal') {
            return;
        }

        DB::transaction(function () use ($sale, $reason) {
            $sale->update([
                'status' => 'batal',
                'notes' => ($sale->notes ? $sale->notes . "\n" : "") . "[DIBATALKAN] " . ($reason ?? 'Dibatalkan oleh pengguna'),
            ]);

            $this->stockService->rollbackSaleDeduction($sale, $reason);
        });
    }
}
