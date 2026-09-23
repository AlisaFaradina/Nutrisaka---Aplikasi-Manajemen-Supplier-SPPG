<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Sppg;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // KPI Ringkasan
        $salesToday = Sale::where('status', 'selesai')
            ->whereDate('sale_date', $today)
            ->sum('total_amount');

        $salesThisMonth = Sale::where('status', 'selesai')
            ->whereBetween('sale_date', [$startOfMonth, Carbon::now()])
            ->sum('total_amount');

        $pendingOrdersCount = Order::whereIn('status', ['menunggu', 'diproses'])->count();

        $activeSppgCount = Sppg::where('is_active', true)->count();

        $totalDebt = Sale::where('status', 'selesai')
            ->where('remaining_balance', '>', 0)
            ->sum('remaining_balance');

        // Produk dengan stok menipis atau habis
        $lowStockProducts = Product::with('category')
            ->where('is_active', true)
            ->whereRaw('current_stock <= min_stock')
            ->orderBy('current_stock', 'asc')
            ->take(6)
            ->get();

        // Pesanan terbaru
        $recentOrders = Order::with('sppg')
            ->latest('order_date')
            ->latest('id')
            ->take(5)
            ->get();

        // Penjualan terbaru
        $recentSales = Sale::with(['sppg'])
            ->where('status', 'selesai')
            ->latest('sale_date')
            ->latest('id')
            ->take(5)
            ->get();

        // Ringkasan penjualan 7 hari terakhir
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayName = $date->translatedFormat('D, d M');
            $amount = (float) Sale::where('status', 'selesai')
                ->whereDate('sale_date', $date)
                ->sum('total_amount');

            $last7Days[] = [
                'date' => $date->toDateString(),
                'label' => $dayName,
                'amount' => $amount,
            ];
        }

        return view('dashboard.index', compact(
            'salesToday',
            'salesThisMonth',
            'pendingOrdersCount',
            'activeSppgCount',
            'totalDebt',
            'lowStockProducts',
            'recentOrders',
            'recentSales',
            'last7Days'
        ));
    }
}
