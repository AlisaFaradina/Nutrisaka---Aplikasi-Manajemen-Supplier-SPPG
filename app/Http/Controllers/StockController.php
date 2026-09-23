<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $query = Product::with('category')->where('is_active', true);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($filter = $request->input('filter')) {
            if ($filter === 'habis') {
                $query->where('current_stock', '<=', 0);
            } elseif ($filter === 'menipis') {
                $query->where('current_stock', '>', 0)->whereRaw('current_stock <= min_stock');
            } elseif ($filter === 'aman') {
                $query->whereRaw('current_stock > min_stock');
            }
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        $stats = [
            'total_items' => Product::where('is_active', true)->count(),
            'safe_stock' => Product::where('is_active', true)->whereRaw('current_stock > min_stock')->count(),
            'low_stock' => Product::where('is_active', true)->where('current_stock', '>', 0)->whereRaw('current_stock <= min_stock')->count(),
            'out_of_stock' => Product::where('is_active', true)->where('current_stock', '<=', 0)->count(),
        ];

        return view('stock.index', compact('products', 'categories', 'stats'));
    }

    public function createIn()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock.in', compact('products'));
    }

    public function storeIn(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'source' => 'required|string|max:50', // purchase, harvest, supplier_upstream, other
            'notes' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $this->stockService->recordStockIn(
            product: $product,
            quantity: (float) $validated['quantity'],
            source: $validated['source'],
            notes: $validated['notes'] ?? 'Barang Masuk / Pembelian Pasokan'
        );

        return redirect()->route('stock.index')
            ->with('success', "Stok {$product->name} berhasil ditambahkan sebanyak {$validated['quantity']} {$product->unit}.");
    }

    public function createAdjustment()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock.adjustment', compact('products'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'physical_qty' => 'required|numeric|min:0',
            'notes' => 'required|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $this->stockService->adjustStock(
            product: $product,
            physicalQty: (float) $validated['physical_qty'],
            notes: $validated['notes']
        );

        return redirect()->route('stock.index')
            ->with('success', "Penyesuaian stok untuk {$product->name} berhasil disimpan. Stok fisik kini: {$validated['physical_qty']} {$product->unit}.");
    }

    public function history(Request $request)
    {
        $query = StockMovement::with('product.category')->latest('id');

        if ($productId = $request->input('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $movements = $query->paginate(20)->withQueryString();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('stock.history', compact('movements', 'products'));
    }
}
