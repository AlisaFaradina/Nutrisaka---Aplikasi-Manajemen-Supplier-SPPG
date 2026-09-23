<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Sppg;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request)
    {
        $query = Order::with(['sppg', 'items.product'])->latest('order_date')->latest('id');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('sppg', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($sppgId = $request->input('sppg_id')) {
            $query->where('sppg_id', $sppgId);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Hitung count per status untuk tab badge
        $counts = [
            'all' => Order::count(),
            'menunggu' => Order::where('status', 'menunggu')->count(),
            'diproses' => Order::where('status', 'diproses')->count(),
            'selesai' => Order::where('status', 'selesai')->count(),
            'dibatalkan' => Order::where('status', 'dibatalkan')->count(),
        ];

        $sppgs = Sppg::where('is_active', true)->orderBy('name')->get();

        return view('orders.index', compact('orders', 'counts', 'sppgs'));
    }

    public function create()
    {
        $sppgs = Sppg::where('is_active', true)->orderBy('name')->get();
        $products = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        $generatedOrderNumber = Order::generateOrderNumber();

        return view('orders.create', compact('sppgs', 'products', 'generatedOrderNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sppg_id' => 'required|exists:sppgs,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:order_date',
            'status' => 'required|in:draft,menunggu,diproses',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $order = $this->orderService->createOrder([
            'sppg_id' => $validated['sppg_id'],
            'order_date' => $validated['order_date'],
            'delivery_date' => $validated['delivery_date'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ], $validated['items']);

        return redirect()->route('orders.show', $order)
            ->with('success', "Pesanan {$order->order_number} berhasil dibuat.");
    }

    public function show(Order $order)
    {
        $order->load(['sppg', 'items.product.category', 'sale']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,menunggu,diproses,dibatalkan,selesai',
        ]);

        $this->orderService->updateStatus($order, $validated['status']);

        return back()->with('success', "Status pesanan diubah menjadi: {$order->status_label}.");
    }

    public function convertToSale(Request $request, Order $order)
    {
        if ($order->sale) {
            return redirect()->route('sales.show', $order->sale)
                ->with('info', "Pesanan ini sudah pernah dikonversi ke penjualan (Invoice: {$order->sale->invoice_number}).");
        }

        $validated = $request->validate([
            'sale_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'initial_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:tunai,transfer,lainnya',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $sale = $this->orderService->convertOrderToSale($order, $validated);

        return redirect()->route('sales.show', $sale)
            ->with('success', "Pesanan berhasil diproses menjadi Transaksi Penjualan {$sale->invoice_number}. Stok produk telah otomatis diperbarui.");
    }
}
