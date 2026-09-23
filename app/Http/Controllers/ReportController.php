<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Sppg;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'sales'); // sales, sppg, products, debts, stock
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $sppgId = $request->input('sppg_id');

        $sppgs = Sppg::where('is_active', true)->orderBy('name')->get();
        $settings = AppSetting::getAllSettings();

        $data = match ($type) {
            'sppg' => $this->getSppgReport($startDate, $endDate),
            'products' => $this->getProductReport($startDate, $endDate),
            'debts' => $this->getDebtReport(),
            'stock' => $this->getStockMovementReport($startDate, $endDate),
            default => $this->getSalesReport($startDate, $endDate, $sppgId),
        };

        if ($request->has('export') && $request->input('export') === 'csv') {
            return $this->exportCsv($type, $data, $startDate, $endDate);
        }

        return view('reports.index', compact(
            'type',
            'startDate',
            'endDate',
            'sppgId',
            'sppgs',
            'data',
            'settings'
        ));
    }

    protected function getSalesReport(string $startDate, string $endDate, ?string $sppgId)
    {
        $query = Sale::with(['sppg', 'items.product'])
            ->where('status', 'selesai')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->latest('sale_date');

        if ($sppgId) {
            $query->where('sppg_id', $sppgId);
        }

        $sales = $query->get();
        $totalRevenue = $sales->sum('total_amount');
        $totalPaid = $sales->sum('paid_amount');
        $totalDebt = $sales->sum('remaining_balance');

        return [
            'sales' => $sales,
            'summary' => [
                'total_transactions' => $sales->count(),
                'total_revenue' => $totalRevenue,
                'total_paid' => $totalPaid,
                'total_debt' => $totalDebt,
            ],
        ];
    }

    protected function getSppgReport(string $startDate, string $endDate)
    {
        $sppgs = Sppg::with(['sales' => function ($q) use ($startDate, $endDate) {
            $q->where('status', 'selesai')->whereBetween('sale_date', [$startDate, $endDate]);
        }])->get()->map(function ($sppg) {
            $salesCount = $sppg->sales->count();
            $totalAmount = $sppg->sales->sum('total_amount');
            $totalPaid = $sppg->sales->sum('paid_amount');
            $totalDebt = $sppg->sales->sum('remaining_balance');

            return [
                'id' => $sppg->id,
                'code' => $sppg->code,
                'name' => $sppg->name,
                'pic_name' => $sppg->pic_name,
                'phone' => $sppg->phone,
                'sales_count' => $salesCount,
                'total_amount' => $totalAmount,
                'total_paid' => $totalPaid,
                'total_debt' => $totalDebt,
            ];
        })->sortByDesc('total_amount')->values();

        return [
            'items' => $sppgs,
            'total_sales' => $sppgs->sum('total_amount'),
            'total_debt' => $sppgs->sum('total_debt'),
        ];
    }

    protected function getProductReport(string $startDate, string $endDate)
    {
        $items = SaleItem::with(['product.category', 'sale'])
            ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'selesai')->whereBetween('sale_date', [$startDate, $endDate]);
            })
            ->get()
            ->groupBy('product_id')
            ->map(function ($group) {
                $product = $group->first()->product;
                $totalQty = $group->sum('quantity');
                $totalSubtotal = $group->sum('subtotal');

                return [
                    'product_name' => $product?->name ?? 'Produk Terhapus',
                    'sku' => $product?->sku ?? '-',
                    'unit' => $product?->unit ?? 'Unit',
                    'category' => $product?->category?->name ?? 'Umum',
                    'total_qty' => $totalQty,
                    'total_amount' => $totalSubtotal,
                ];
            })
            ->sortByDesc('total_amount')
            ->values();

        return [
            'items' => $items,
            'total_amount' => $items->sum('total_amount'),
        ];
    }

    protected function getDebtReport()
    {
        $sales = Sale::with('sppg')
            ->where('status', 'selesai')
            ->where('remaining_balance', '>', 0)
            ->orderBy('due_date', 'asc')
            ->get();

        return [
            'sales' => $sales,
            'total_debt' => $sales->sum('remaining_balance'),
            'count' => $sales->count(),
        ];
    }

    protected function getStockMovementReport(string $startDate, string $endDate)
    {
        $movements = StockMovement::with('product.category')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest('id')
            ->get();

        return [
            'movements' => $movements,
            'in_count' => $movements->where('type', 'in')->count(),
            'out_count' => $movements->where('type', 'out')->count(),
            'adj_count' => $movements->where('type', 'adjustment')->count(),
        ];
    }

    protected function exportCsv(string $type, array $data, string $startDate, string $endDate): StreamedResponse
    {
        $filename = "laporan_{$type}_{$startDate}_sd_{$endDate}.csv";

        return response()->streamDownload(function () use ($type, $data) {
            $handle = fopen('php://output', 'w');
            // Add BOM for Excel UTF-8
            fputs($handle, "\xEF\xBB\xBF");

            if ($type === 'sales') {
                fputcsv($handle, ['No. Invoice', 'Tanggal', 'SPPG', 'Subtotal', 'Diskon', 'Total Akhir', 'Dibayar', 'Sisa Piutang', 'Status Pembayaran']);
                foreach ($data['sales'] as $sale) {
                    fputcsv($handle, [
                        $sale->invoice_number,
                        $sale->sale_date->format('d/m/Y'),
                        $sale->sppg->name,
                        $sale->subtotal,
                        $sale->discount,
                        $sale->total_amount,
                        $sale->paid_amount,
                        $sale->remaining_balance,
                        $sale->payment_status_label,
                    ]);
                }
            } elseif ($type === 'sppg') {
                fputcsv($handle, ['Kode SPPG', 'Nama SPPG', 'Penanggung Jawab', 'No. HP', 'Jumlah Transaksi', 'Total Penjualan', 'Total Terbayar', 'Sisa Piutang']);
                foreach ($data['items'] as $item) {
                    fputcsv($handle, [
                        $item['code'],
                        $item['name'],
                        $item['pic_name'],
                        $item['phone'],
                        $item['sales_count'],
                        $item['total_amount'],
                        $item['total_paid'],
                        $item['total_debt'],
                    ]);
                }
            } elseif ($type === 'products') {
                fputcsv($handle, ['Kode SKU', 'Nama Produk', 'Kategori', 'Satuan', 'Kuantitas Terjual', 'Total Omset']);
                foreach ($data['items'] as $item) {
                    fputcsv($handle, [
                        $item['sku'],
                        $item['product_name'],
                        $item['category'],
                        $item['unit'],
                        $item['total_qty'],
                        $item['total_amount'],
                    ]);
                }
            } elseif ($type === 'debts') {
                fputcsv($handle, ['No. Invoice', 'Tanggal Jual', 'Jatuh Tempo', 'SPPG', 'Total Tagihan', 'Sudah Dibayar', 'Sisa Piutang']);
                foreach ($data['sales'] as $sale) {
                    fputcsv($handle, [
                        $sale->invoice_number,
                        $sale->sale_date->format('d/m/Y'),
                        $sale->due_date ? $sale->due_date->format('d/m/Y') : '-',
                        $sale->sppg->name,
                        $sale->total_amount,
                        $sale->paid_amount,
                        $sale->remaining_balance,
                    ]);
                }
            } elseif ($type === 'stock') {
                fputcsv($handle, ['Waktu Mutasi', 'Produk', 'Jenis', 'Kuantitas', 'Stok Sebelum', 'Stok Sesudah', 'Keterangan']);
                foreach ($data['movements'] as $m) {
                    fputcsv($handle, [
                        $m->created_at->format('d/m/Y H:i'),
                        $m->product?->name,
                        $m->type_label,
                        $m->quantity . ' ' . $m->product?->unit,
                        $m->before_stock,
                        $m->after_stock,
                        $m->notes,
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
