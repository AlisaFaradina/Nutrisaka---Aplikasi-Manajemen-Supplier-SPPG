<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Sppg;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    public function index(Request $request)
    {
        $query = Sale::with(['sppg', 'order'])->latest('sale_date')->latest('id');

        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($sppgId = $request->input('sppg_id')) {
            $query->where('sppg_id', $sppgId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('sppg', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sales = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Sale::where('status', 'selesai')->count(),
            'belum_bayar' => Sale::where('status', 'selesai')->where('payment_status', 'belum_bayar')->count(),
            'sebagian' => Sale::where('status', 'selesai')->where('payment_status', 'sebagian')->count(),
            'lunas' => Sale::where('status', 'selesai')->where('payment_status', 'lunas')->count(),
        ];

        $sppgs = Sppg::where('is_active', true)->orderBy('name')->get();

        return view('sales.index', compact('sales', 'counts', 'sppgs'));
    }

    public function create()
    {
        $sppgs = Sppg::where('is_active', true)->orderBy('name')->get();
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        $generatedInvoiceNumber = Sale::generateInvoiceNumber();

        return view('sales.create', compact('sppgs', 'products', 'generatedInvoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sppg_id' => 'required|exists:sppgs,id',
            'sale_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:sale_date',
            'discount' => 'nullable|numeric|min:0',
            'initial_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:tunai,transfer,lainnya',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $sale = $this->saleService->createSale([
            'sppg_id' => $validated['sppg_id'],
            'sale_date' => $validated['sale_date'],
            'due_date' => $validated['due_date'] ?? null,
            'discount' => $validated['discount'] ?? 0,
            'initial_paid' => $validated['initial_paid'] ?? 0,
            'payment_method' => $validated['payment_method'] ?? 'tunai',
            'payment_reference' => $validated['payment_reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ], $validated['items']);

        return redirect()->route('sales.show', $sale)
            ->with('success', "Penjualan {$sale->invoice_number} berhasil dicatat dan stok produk telah dipotong.");
    }

    public function show(Sale $sale)
    {
        $sale->load(['sppg', 'order', 'items.product.category', 'payments']);
        $settings = AppSetting::getAllSettings();

        return view('sales.show', compact('sale', 'settings'));
    }

    public function printInvoice(Sale $sale)
    {
        $sale->load(['sppg', 'order', 'items.product', 'payments']);
        $settings = AppSetting::getAllSettings();

        return view('sales.print_invoice', compact('sale', 'settings'));
    }

    public function printThermal(Sale $sale)
    {
        $sale->load(['sppg', 'order', 'items.product', 'payments']);
        $settings = AppSetting::getAllSettings();

        return view('sales.print_thermal', compact('sale', 'settings'));
    }

    public function cancel(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $this->saleService->cancelSale($sale, $validated['reason']);

        return redirect()->route('sales.show', $sale)
            ->with('success', "Penjualan {$sale->invoice_number} telah dibatalkan dan stok produk telah dikembalikan.");
    }
}
