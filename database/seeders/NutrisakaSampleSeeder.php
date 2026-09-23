<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NutrisakaSampleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // ====================================================================
            // BERSIHKAN data transaksional terlebih dahulu agar seeder idempotent.
            // Urutan: child → parent untuk menghindari FK constraint.
            // ====================================================================
            DB::statement('PRAGMA foreign_keys = OFF');

            StockMovement::truncate();
            Payment::truncate();
            SaleItem::truncate();
            Sale::truncate();
            OrderItem::truncate();
            Order::truncate();

            DB::statement('PRAGMA foreign_keys = ON');

            // ================================================================
            // 1. Settings — updateOrCreate agar aman dijalankan ulang
            // ================================================================
            $settings = [
                'supplier_name' => 'Nutrisaka Pangan Nusantara',
                'supplier_tagline' => 'Mitra Terpercaya Pasokan Bahan Pangan & Nutrisi SPPG',
                'supplier_phone' => '0812-8877-6655',
                'supplier_email' => 'logistik.nutrisaka@gmail.com',
                'supplier_address' => 'Kawasan Pergudangan Pangan Mandiri Blok B-12, Jawa Tengah',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '137-00-8899776-5',
                'bank_account_name' => 'CV NUTRISAKA PANGAN NUSANTARA',
                'invoice_footer_notes' => 'Barang diterima dalam kondisi segar dan baik. Pembayaran transfer harap konfirmasi ke WhatsApp Admin.',
                'security_pin' => '1234',
                'thermal_paper_size' => '58mm',
            ];

            foreach ($settings as $k => $v) {
                AppSetting::set($k, $v);
            }

            // ================================================================
            // 2. SPPGs — updateOrCreate berdasarkan kode unik
            // ================================================================
            $sppgsData = [
                [
                    'code' => 'SPPG-001',
                    'name' => 'SPPG Al-Hikmah',
                    'pic_name' => 'Pak Budi',
                    'phone' => '0812-3456-7890',
                    'address' => 'Jl. Juwana-Tayu KM 15, Desa Cebolek, Margoyoso',
                    'notes' => 'Kebutuhan porsi 250 anak per hari. Pengiriman jam 05.30 pagi.',
                    'is_active' => true,
                ],
                [
                    'code' => 'SPPG-002',
                    'name' => 'SPPG Kajar',
                    'pic_name' => 'Bpk. Ahmad Hidayat',
                    'phone' => '0813-8899-1122',
                    'address' => 'Area Sawah, Kajar, Trangkil, Pati',
                    'notes' => 'Plafon kredit tempo 14 hari. Rutin pesan telur dan ayam.',
                    'is_active' => true,
                ],
                [
                    'code' => 'SPPG-003',
                    'name' => 'SPPG Satuan Pelayanan SDN 01 Terpadu',
                    'pic_name' => 'Ibu Sri Wahyuni, S.Pd',
                    'phone' => '0857-1234-5678',
                    'address' => 'Komplek Sekolah Dasar Terpadu No. 8',
                    'notes' => 'Pengiriman hari Senin dan Kamis.',
                    'is_active' => true,
                ],
                [
                    'code' => 'SPPG-004',
                    'name' => 'SPPG Dapur Nutrisi Berkah Bersama',
                    'pic_name' => 'Bpk. Hendra Gunawan',
                    'phone' => '0819-7654-3210',
                    'address' => 'Sentra Dapur Sehat Mandiri Blok C No. 3',
                    'notes' => 'Pembayaran transfer bank rutin setiap hari Jumat.',
                    'is_active' => true,
                ],
                [
                    'code' => 'SPPG-005',
                    'name' => 'SPPG Bina Generasi Emas',
                    'pic_name' => 'Ibu Rina Marlina',
                    'phone' => '0821-9988-7766',
                    'address' => 'Jl. Melati Indah No. 88',
                    'notes' => 'Unit baru mulai beroperasi bulan ini.',
                    'is_active' => true,
                ],
            ];

            $sppgModels = [];
            foreach ($sppgsData as $sd) {
                $sppgModels[] = Sppg::updateOrCreate(['code' => $sd['code']], $sd);
            }

            // ================================================================
            // 3. Categories — firstOrCreate berdasarkan nama
            // ================================================================
            $cats = [
                'Beras & Serealia' => 'Bahan pokok karbohidrat utama',
                'Protein Ayam & Daging' => 'Daging ayam potong segar dan daging sapi grade A',
                'Telur & Hasil Ternak' => 'Telur ayam ras segar dan olahan peternakan',
                'Sayuran Segar' => 'Sayur hijau segar panen harian',
                'Buah-buahan Segar' => 'Buah segar pencuci mulut bergizi',
                'Susu & Olahan Gizi' => 'Susu kemasan UHT dan olahan kedelai tempe tahu',
                'Bumbu & Minyak' => 'Minyak goreng kelapa sawit dan bumbu masak dapur',
            ];

            $catModels = [];
            foreach ($cats as $cName => $cDesc) {
                $catModels[$cName] = Category::firstOrCreate(['name' => $cName], ['description' => $cDesc]);
            }

            // ================================================================
            // 4. Products — updateOrCreate berdasarkan SKU, reset stok awal
            // ================================================================
            $productsData = [
                [
                    'sku' => 'PRD-001',
                    'category_id' => $catModels['Beras & Serealia']->id,
                    'name' => 'Beras Premium Rojolele (Sak 25kg)',
                    'unit' => 'Sak 25kg',
                    'selling_price' => 375000,
                    'cost_price' => 345000,
                    'current_stock' => 45,
                    'min_stock' => 10,
                    'description' => 'Beras pulen kualitas premium tanpa pemutih',
                ],
                [
                    'sku' => 'PRD-002',
                    'category_id' => $catModels['Beras & Serealia']->id,
                    'name' => 'Beras Slyp Super Ramos (Sak 50kg)',
                    'unit' => 'Sak 50kg',
                    'selling_price' => 730000,
                    'cost_price' => 680000,
                    'current_stock' => 20,
                    'min_stock' => 5,
                    'description' => 'Kemasan karung besar untuk dapur porsi banyak',
                ],
                [
                    'sku' => 'PRD-003',
                    'category_id' => $catModels['Protein Ayam & Daging']->id,
                    'name' => 'Daging Ayam Karkas Broiler Segar (Kg)',
                    'unit' => 'Kg',
                    'selling_price' => 38500,
                    'cost_price' => 33000,
                    'current_stock' => 120,
                    'min_stock' => 20,
                    'description' => 'Ayam bersih tanpa ceker dan kepala, potong subuh',
                ],
                [
                    'sku' => 'PRD-004',
                    'category_id' => $catModels['Protein Ayam & Daging']->id,
                    'name' => 'Daging Sapi Pilihan Semur / Rawon (Kg)',
                    'unit' => 'Kg',
                    'selling_price' => 135000,
                    'cost_price' => 120000,
                    'current_stock' => 30,
                    'min_stock' => 8,
                    'description' => 'Daging sapi segar lokal rendah lemak',
                ],
                [
                    'sku' => 'PRD-005',
                    'category_id' => $catModels['Telur & Hasil Ternak']->id,
                    'name' => 'Telur Ayam Negeri Segar (Tray 30 Btr)',
                    'unit' => 'Tray (30 Btr)',
                    'selling_price' => 52000,
                    'cost_price' => 46000,
                    'current_stock' => 75,
                    'min_stock' => 15,
                    'description' => 'Telur ayam ras segar grade A cangkang tebal',
                ],
                [
                    'sku' => 'PRD-006',
                    'category_id' => $catModels['Sayuran Segar']->id,
                    'name' => 'Wortel Manis Brastagi (Kg)',
                    'unit' => 'Kg',
                    'selling_price' => 14000,
                    'cost_price' => 11000,
                    'current_stock' => 60,
                    'min_stock' => 15,
                    'description' => 'Wortel segar kaya vitamin A untuk sop anak',
                ],
                [
                    'sku' => 'PRD-007',
                    'category_id' => $catModels['Sayuran Segar']->id,
                    'name' => 'Brokoli Hijau Super Segar (Kg)',
                    'unit' => 'Kg',
                    'selling_price' => 28000,
                    'cost_price' => 23000,
                    'current_stock' => 4, // Menipis
                    'min_stock' => 10,
                    'description' => 'Brokoli hijau segar nutrisi tinggi',
                ],
                [
                    'sku' => 'PRD-008',
                    'category_id' => $catModels['Sayuran Segar']->id,
                    'name' => 'Buncis Baby Super (Kg)',
                    'unit' => 'Kg',
                    'selling_price' => 18000,
                    'cost_price' => 14000,
                    'current_stock' => 0, // Habis
                    'min_stock' => 10,
                    'description' => 'Buncis muda renyah',
                ],
                [
                    'sku' => 'PRD-009',
                    'category_id' => $catModels['Buah-buahan Segar']->id,
                    'name' => 'Pisang Cavendish Manis (Kg)',
                    'unit' => 'Kg',
                    'selling_price' => 22000,
                    'cost_price' => 17000,
                    'current_stock' => 45,
                    'min_stock' => 10,
                    'description' => 'Pisang manis kulit bersih matang pas',
                ],
                [
                    'sku' => 'PRD-010',
                    'category_id' => $catModels['Susu & Olahan Gizi']->id,
                    'name' => 'Susu UHT Kemasan Gizi 200ml (Karton)',
                    'unit' => 'Karton',
                    'selling_price' => 115000,
                    'cost_price' => 100000,
                    'current_stock' => 40,
                    'min_stock' => 10,
                    'description' => '1 karton isi 24 botol/kotak susu siap minum',
                ],
                [
                    'sku' => 'PRD-011',
                    'category_id' => $catModels['Bumbu & Minyak']->id,
                    'name' => 'Minyak Goreng Kelapa Sawit (Jerigen 5L)',
                    'unit' => 'Jerigen',
                    'selling_price' => 82000,
                    'cost_price' => 74000,
                    'current_stock' => 30,
                    'min_stock' => 8,
                    'description' => 'Minyak goreng higienis jernih fortifikasi Vit A',
                ],
            ];

            $productModels = [];
            foreach ($productsData as $pd) {
                $productModels[] = Product::updateOrCreate(['sku' => $pd['sku']], $pd);
            }

            // ================================================================
            // 5. Initial stock movements for products (saldo awal)
            // ================================================================
            foreach ($productModels as $pm) {
                if ($pm->current_stock > 0) {
                    StockMovement::create([
                        'product_id' => $pm->id,
                        'type' => 'in',
                        'quantity' => $pm->current_stock,
                        'before_stock' => 0,
                        'after_stock' => $pm->current_stock,
                        'reference_type' => 'initial',
                        'notes' => 'Saldo awal stok gudang saat setup data',
                    ]);
                }
            }

            // ================================================================
            // 6. Sample Orders
            // ================================================================
            $today = now()->format('Ymd');

            // Order 1: Status MENUNGGU
            $ord1 = Order::create([
                'order_number' => "ORD-{$today}-0001",
                'sppg_id' => $sppgModels[0]->id,
                'order_date' => now()->toDateString(),
                'delivery_date' => now()->addDay()->toDateString(),
                'status' => 'menunggu',
                'total_amount' => 1970000,
                'notes' => 'Pesanan rutin kebutuhan makan siang SPPG Al-Hikmah',
            ]);
            OrderItem::create(['order_id' => $ord1->id, 'product_id' => $productModels[0]->id, 'quantity' => 2, 'unit_price' => 375000, 'subtotal' => 750000]);
            OrderItem::create(['order_id' => $ord1->id, 'product_id' => $productModels[2]->id, 'quantity' => 20, 'unit_price' => 38500, 'subtotal' => 770000]);
            OrderItem::create(['order_id' => $ord1->id, 'product_id' => $productModels[4]->id, 'quantity' => 5, 'unit_price' => 52000, 'subtotal' => 260000]);
            OrderItem::create(['order_id' => $ord1->id, 'product_id' => $productModels[5]->id, 'quantity' => 13.5, 'unit_price' => 14000, 'subtotal' => 190000]);

            // Order 2: Status DIPROSES
            $ord2 = Order::create([
                'order_number' => "ORD-{$today}-0002",
                'sppg_id' => $sppgModels[1]->id,
                'order_date' => now()->subDay()->toDateString(),
                'delivery_date' => now()->toDateString(),
                'status' => 'diproses',
                'total_amount' => 1575000,
                'notes' => 'Sedang dalam proses sortir sayur dan pengemasan daging',
            ]);
            OrderItem::create(['order_id' => $ord2->id, 'product_id' => $productModels[2]->id, 'quantity' => 25, 'unit_price' => 38500, 'subtotal' => 962500]);
            OrderItem::create(['order_id' => $ord2->id, 'product_id' => $productModels[8]->id, 'quantity' => 15, 'unit_price' => 22000, 'subtotal' => 330000]);
            OrderItem::create(['order_id' => $ord2->id, 'product_id' => $productModels[5]->id, 'quantity' => 20, 'unit_price' => 14000, 'subtotal' => 280000]);

            // ================================================================
            // 7. Completed Sales with various payment statuses
            // ================================================================

            // Sale 1: LUNAS (Paid in Full)
            $sale1 = Sale::create([
                'invoice_number' => "INV-{$today}-0001",
                'order_id' => null,
                'sppg_id' => $sppgModels[0]->id,
                'sale_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'subtotal' => 2635000,
                'discount' => 35000,
                'total_amount' => 2600000,
                'paid_amount' => 2600000,
                'remaining_balance' => 0,
                'payment_status' => 'lunas',
                'status' => 'selesai',
                'notes' => 'Pengiriman pagi lengkap armada pick up Box 1',
            ]);
            SaleItem::create(['sale_id' => $sale1->id, 'product_id' => $productModels[0]->id, 'quantity' => 4, 'unit_price' => 375000, 'subtotal' => 1500000]);
            SaleItem::create(['sale_id' => $sale1->id, 'product_id' => $productModels[2]->id, 'quantity' => 20, 'unit_price' => 38500, 'subtotal' => 770000]);
            SaleItem::create(['sale_id' => $sale1->id, 'product_id' => $productModels[9]->id, 'quantity' => 3, 'unit_price' => 115000, 'subtotal' => 345000]);
            SaleItem::create(['sale_id' => $sale1->id, 'product_id' => $productModels[5]->id, 'quantity' => 15, 'unit_price' => 14000, 'subtotal' => 210000]);

            Payment::create([
                'payment_number' => "PAY-{$today}-0001",
                'sale_id' => $sale1->id,
                'payment_date' => now()->toDateString(),
                'amount' => 2600000,
                'payment_method' => 'transfer',
                'reference_number' => 'TRF-MDR-889102',
                'notes' => 'Pelunasan faktur via transfer Bank Mandiri',
            ]);

            // Sale 2: SEBAGIAN (Partial Payment, Active Debt)
            $sale2Date = now()->subDays(2)->format('Ymd');
            $sale2 = Sale::create([
                'invoice_number' => "INV-{$sale2Date}-0002",
                'order_id' => null,
                'sppg_id' => $sppgModels[1]->id,
                'sale_date' => now()->subDays(2)->toDateString(),
                'due_date' => now()->addDays(5)->toDateString(),
                'subtotal' => 3450000,
                'discount' => 50000,
                'total_amount' => 3400000,
                'paid_amount' => 1500000,
                'remaining_balance' => 1900000,
                'payment_status' => 'sebagian',
                'status' => 'selesai',
                'notes' => 'DP diterima Rp 1.500.000, sisa Rp 1.900.000 tempo 7 hari',
            ]);
            SaleItem::create(['sale_id' => $sale2->id, 'product_id' => $productModels[1]->id, 'quantity' => 2, 'unit_price' => 730000, 'subtotal' => 1460000]);
            SaleItem::create(['sale_id' => $sale2->id, 'product_id' => $productModels[3]->id, 'quantity' => 10, 'unit_price' => 135000, 'subtotal' => 1350000]);
            SaleItem::create(['sale_id' => $sale2->id, 'product_id' => $productModels[4]->id, 'quantity' => 12, 'unit_price' => 52000, 'subtotal' => 624000]);

            Payment::create([
                'payment_number' => "PAY-{$sale2Date}-0002",
                'sale_id' => $sale2->id,
                'payment_date' => now()->subDays(2)->toDateString(),
                'amount' => 1500000,
                'payment_method' => 'transfer',
                'reference_number' => 'REF-BCA-554433',
                'notes' => 'Pembayaran uang muka awal',
            ]);

            // Sale 3: BELUM BAYAR (Full Debt)
            $sale3Date = now()->subDay()->format('Ymd');
            $sale3 = Sale::create([
                'invoice_number' => "INV-{$sale3Date}-0003",
                'order_id' => null,
                'sppg_id' => $sppgModels[2]->id,
                'sale_date' => now()->subDay()->toDateString(),
                'due_date' => now()->addDays(3)->toDateString(),
                'subtotal' => 1845000,
                'discount' => 0,
                'total_amount' => 1845000,
                'paid_amount' => 0,
                'remaining_balance' => 1845000,
                'payment_status' => 'belum_bayar',
                'status' => 'selesai',
                'notes' => 'Pengiriman sayuran & telur ke SDN 01 Terpadu. Menunggu pencairan SPPG.',
            ]);
            SaleItem::create(['sale_id' => $sale3->id, 'product_id' => $productModels[4]->id, 'quantity' => 15, 'unit_price' => 52000, 'subtotal' => 780000]);
            SaleItem::create(['sale_id' => $sale3->id, 'product_id' => $productModels[2]->id, 'quantity' => 15, 'unit_price' => 38500, 'subtotal' => 577500]);
            SaleItem::create(['sale_id' => $sale3->id, 'product_id' => $productModels[10]->id, 'quantity' => 6, 'unit_price' => 82000, 'subtotal' => 492000]);

            // ================================================================
            // 8. Log stock movements for the sales (barang keluar)
            // ================================================================
            foreach ([$sale1, $sale2, $sale3] as $s) {
                $s->load('items.product', 'sppg');
                foreach ($s->items as $item) {
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'before_stock' => $item->product->current_stock + $item->quantity,
                        'after_stock' => $item->product->current_stock,
                        'reference_type' => 'sale',
                        'reference_id' => $s->id,
                        'notes' => "Penjualan Faktur {$s->invoice_number} ke SPPG {$s->sppg->name}",
                    ]);
                }
            }
        });
    }
}
