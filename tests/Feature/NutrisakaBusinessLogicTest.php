<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sppg;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\SaleService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NutrisakaBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    protected StockService $stockService;
    protected OrderService $orderService;
    protected SaleService $saleService;
    protected PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockService = app(StockService::class);
        $this->paymentService = app(PaymentService::class);
        $this->saleService = app(SaleService::class);
        $this->orderService = app(OrderService::class);
    }

    public function test_stock_in_increases_quantity_and_records_movement(): void
    {
        $category = Category::create(['name' => 'Beras & Serealia']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRD-TEST-1',
            'name' => 'Beras Premium 25kg',
            'unit' => 'Sak 25kg',
            'selling_price' => 350000,
            'current_stock' => 10,
            'min_stock' => 5,
        ]);

        $this->stockService->recordStockIn($product, 15, 'purchase', 'Pengiriman dari distributor');

        $product->refresh();
        $this->assertEquals(25, $product->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 15,
            'before_stock' => 10,
            'after_stock' => 25,
        ]);
    }

    public function test_order_does_not_deduct_stock_until_sale_is_completed(): void
    {
        $category = Category::create(['name' => 'Protein Hewani']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRD-TEST-2',
            'name' => 'Ayam Karkas Segar',
            'unit' => 'Kg',
            'selling_price' => 38000,
            'current_stock' => 50,
            'min_stock' => 10,
        ]);

        $sppg = Sppg::create([
            'code' => 'SPPG-001',
            'name' => 'SPPG Dapur Sehat Harapan',
            'pic_name' => 'Budi Santoso',
            'phone' => '08123456789',
        ]);

        // Buat pesanan (status: menunggu)
        $order = $this->orderService->createOrder(
            ['sppg_id' => $sppg->id, 'status' => 'menunggu'],
            [['product_id' => $product->id, 'quantity' => 20, 'unit_price' => 38000]]
        );

        $product->refresh();
        // Stok produk TIDAK BOLEH berkurang saat masih order
        $this->assertEquals(50, $product->current_stock);

        // Konversi order ke penjualan
        $sale = $this->orderService->convertOrderToSale($order);

        $product->refresh();
        // Stok berkurang tepat setelah penjualan tercipta
        $this->assertEquals(30, $product->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 20,
            'before_stock' => 50,
            'after_stock' => 30,
        ]);

        $this->assertEquals('selesai', $order->refresh()->status);
        $this->assertEquals(760000, $sale->total_amount);
        $this->assertEquals('belum_bayar', $sale->payment_status);
        $this->assertEquals(760000, $sale->remaining_balance);
    }

    public function test_payment_service_calculates_partial_and_full_payment(): void
    {
        $category = Category::create(['name' => 'Sayuran']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PRD-TEST-3',
            'name' => 'Wortel Segar',
            'unit' => 'Kg',
            'selling_price' => 15000,
            'current_stock' => 100,
        ]);

        $sppg = Sppg::create([
            'code' => 'SPPG-002',
            'name' => 'SPPG Ceria Nusantara',
        ]);

        $sale = $this->saleService->createSale(
            ['sppg_id' => $sppg->id, 'sale_date' => now()->toDateString()],
            [['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 15000]]
        );

        $this->assertEquals(150000, $sale->total_amount);
        $this->assertEquals('belum_bayar', $sale->payment_status);
        $this->assertEquals(150000, $sale->remaining_balance);

        // Pembayaran pertama (sebagian: 50.000)
        $this->paymentService->recordPayment($sale, 50000, 'transfer');
        $sale->refresh();

        $this->assertEquals(50000, $sale->paid_amount);
        $this->assertEquals(100000, $sale->remaining_balance);
        $this->assertEquals('sebagian', $sale->payment_status);

        // Pembayaran kedua (pelunasan sisa 100.000)
        $this->paymentService->recordPayment($sale, 100000, 'tunai');
        $sale->refresh();

        $this->assertEquals(150000, $sale->paid_amount);
        $this->assertEquals(0, $sale->remaining_balance);
        $this->assertEquals('lunas', $sale->payment_status);
    }
}
