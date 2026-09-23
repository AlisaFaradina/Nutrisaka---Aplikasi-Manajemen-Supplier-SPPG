<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Sppg;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class BackupService
{
    /**
     * Ekspor seluruh data lokal ke dalam struktur JSON.
     */
    public function exportBackup(): array
    {
        return [
            'app' => 'Nutrisaka Supplier SPPG',
            'version' => '1.0.0',
            'exported_at' => now()->toIso8601String(),
            'data' => [
                'app_settings' => AppSetting::all()->toArray(),
                'categories' => Category::all()->toArray(),
                'products' => Product::all()->toArray(),
                'sppgs' => Sppg::all()->toArray(),
                'orders' => Order::all()->toArray(),
                'order_items' => OrderItem::all()->toArray(),
                'sales' => Sale::all()->toArray(),
                'sale_items' => SaleItem::all()->toArray(),
                'payments' => Payment::all()->toArray(),
                'stock_movements' => StockMovement::all()->toArray(),
            ],
        ];
    }

    /**
     * Pulihkan data dari array JSON backup.
     */
    public function restoreBackup(array $payload): void
    {
        if (!isset($payload['app']) || $payload['app'] !== 'Nutrisaka Supplier SPPG' || !isset($payload['data'])) {
            throw new Exception('Format file backup tidak valid untuk aplikasi Nutrisaka.');
        }

        $data = $payload['data'];

        DB::transaction(function () use ($data) {
            // Nonaktifkan foreign keys sementara untuk restore bersih di SQLite
            DB::statement('PRAGMA foreign_keys = OFF');

            StockMovement::truncate();
            Payment::truncate();
            SaleItem::truncate();
            Sale::truncate();
            OrderItem::truncate();
            Order::truncate();
            Product::truncate();
            Category::truncate();
            Sppg::truncate();
            AppSetting::truncate();

            if (!empty($data['app_settings'])) {
                AppSetting::insert($data['app_settings']);
            }
            if (!empty($data['categories'])) {
                Category::insert($data['categories']);
            }
            if (!empty($data['sppgs'])) {
                Sppg::insert($data['sppgs']);
            }
            if (!empty($data['products'])) {
                Product::insert($data['products']);
            }
            if (!empty($data['orders'])) {
                Order::insert($data['orders']);
            }
            if (!empty($data['order_items'])) {
                OrderItem::insert($data['order_items']);
            }
            if (!empty($data['sales'])) {
                Sale::insert($data['sales']);
            }
            if (!empty($data['sale_items'])) {
                SaleItem::insert($data['sale_items']);
            }
            if (!empty($data['payments'])) {
                Payment::insert($data['payments']);
            }
            if (!empty($data['stock_movements'])) {
                StockMovement::insert($data['stock_movements']);
            }

            DB::statement('PRAGMA foreign_keys = ON');
        });
    }
}
