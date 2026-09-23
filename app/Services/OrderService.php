<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    /**
     * Membuat pesanan baru dari SPPG.
     */
    public function createOrder(array $orderData, array $items): Order
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Pesanan harus memiliki minimal satu item produk.');
        }

        return DB::transaction(function () use ($orderData, $items) {
            $totalAmount = 0;
            $preparedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (float) $item['quantity'];
                $price = isset($item['unit_price']) ? (float) $item['unit_price'] : (float) $product->selling_price;
                $subtotal = $qty * $price;
                $totalAmount += $subtotal;

                $preparedItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'sppg_id' => $orderData['sppg_id'],
                'order_date' => $orderData['order_date'] ?? now()->toDateString(),
                'delivery_date' => $orderData['delivery_date'] ?? null,
                'status' => $orderData['status'] ?? 'menunggu',
                'total_amount' => $totalAmount,
                'notes' => $orderData['notes'] ?? null,
            ]);

            foreach ($preparedItems as $prepItem) {
                $prepItem['order_id'] = $order->id;
                OrderItem::create($prepItem);
            }

            return $order;
        });
    }

    /**
     * Update status pesanan (Draft, Menunggu, Diproses, Dibatalkan).
     */
    public function updateStatus(Order $order, string $newStatus): void
    {
        $validStatuses = ['draft', 'menunggu', 'diproses', 'dibatalkan', 'selesai'];
        if (!in_array($newStatus, $validStatuses, true)) {
            throw new InvalidArgumentException("Status '{$newStatus}' tidak valid.");
        }

        $order->update(['status' => $newStatus]);
    }

    /**
     * Proses pesanan yang selesai menjadi Transaksi Penjualan & Invoice resmi.
     */
    public function convertOrderToSale(Order $order, array $saleOptions = []): Sale
    {
        if ($order->sale) {
            return $order->sale; // Sudah pernah diproses
        }

        $saleData = [
            'sppg_id' => $order->sppg_id,
            'sale_date' => $saleOptions['sale_date'] ?? now()->toDateString(),
            'due_date' => $saleOptions['due_date'] ?? $order->delivery_date,
            'discount' => $saleOptions['discount'] ?? 0,
            'initial_paid' => $saleOptions['initial_paid'] ?? 0,
            'payment_method' => $saleOptions['payment_method'] ?? 'transfer',
            'payment_reference' => $saleOptions['payment_reference'] ?? null,
            'notes' => "Dibuat otomatis dari Pesanan No. {$order->order_number}. " . ($saleOptions['notes'] ?? ''),
        ];

        $items = [];
        foreach ($order->items as $orderItem) {
            $items[] = [
                'product_id' => $orderItem->product_id,
                'quantity' => $orderItem->quantity,
                'unit_price' => $orderItem->unit_price,
            ];
        }

        return $this->saleService->createSale($saleData, $items, $order);
    }
}
