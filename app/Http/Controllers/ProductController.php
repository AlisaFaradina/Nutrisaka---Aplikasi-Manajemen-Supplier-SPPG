<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $query = Product::with('category');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($status = $request->input('stock_status')) {
            if ($status === 'habis') {
                $query->where('current_stock', '<=', 0);
            } elseif ($status === 'menipis') {
                $query->where('current_stock', '>', 0)->whereRaw('current_stock <= min_stock');
            } elseif ($status === 'aman') {
                $query->whereRaw('current_stock > min_stock');
            }
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->input('active') === '1');
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $generatedSku = Product::generateSku();
        $commonUnits = ['Kg', 'Sak 25kg', 'Sak 50kg', 'Tray (30 Btr)', 'Karton', 'Liter', 'Ikat', 'Pack', 'Pcs'];

        return view('products.create', compact('categories', 'generatedSku', 'commonUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'initial_stock' => 'nullable|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $initialStock = (float) ($validated['initial_stock'] ?? 0);
        unset($validated['initial_stock']);
        $validated['current_stock'] = 0; // will be updated via stockService if initialStock > 0
        $validated['is_active'] = $request->has('is_active');
        $validated['cost_price'] = $validated['cost_price'] ?? 0;

        $product = Product::create($validated);

        if ($initialStock > 0) {
            $this->stockService->recordStockIn(
                product: $product,
                quantity: $initialStock,
                source: 'initial',
                notes: 'Pencatatan saldo stok awal saat produk didaftarkan'
            );
        }

        return redirect()->route('products.index')
            ->with('success', "Produk {$product->name} berhasil ditambahkan.");
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $commonUnits = ['Kg', 'Sak 25kg', 'Sak 50kg', 'Tray (30 Btr)', 'Karton', 'Liter', 'Ikat', 'Pack', 'Pcs'];

        return view('products.edit', compact('product', 'categories', 'commonUnits'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['cost_price'] = $validated['cost_price'] ?? 0;

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', "Produk {$product->name} berhasil diperbarui.");
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->count() > 0 || $product->orderItems()->count() > 0) {
            return back()->with('error', "Produk {$product->name} tidak dapat dihapus karena sudah memiliki histori transaksi. Anda dapat menonaktifkan produk ini.");
        }

        $name = $product->name;
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Produk {$name} berhasil dihapus.");
    }
}
