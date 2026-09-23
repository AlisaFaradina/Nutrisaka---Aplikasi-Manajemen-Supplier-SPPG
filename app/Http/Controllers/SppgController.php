<?php

namespace App\Http\Controllers;

use App\Models\Sppg;
use Illuminate\Http\Request;

class SppgController extends Controller
{
    public function index(Request $request)
    {
        $query = Sppg::query()->withCount(['orders', 'sales']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('pic_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $sppgs = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('sppgs.index', compact('sppgs'));
    }

    public function create()
    {
        $generatedCode = Sppg::generateCode();
        return view('sppgs.create', compact('generatedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:sppgs,code',
            'name' => 'required|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $sppg = Sppg::create($validated);

        return redirect()->route('sppgs.show', $sppg)
            ->with('success', "Data SPPG {$sppg->name} berhasil ditambahkan.");
    }

    public function show(Sppg $sppg)
    {
        $sppg->load([
            'orders' => fn($q) => $q->latest()->take(10),
            'sales' => fn($q) => $q->with('payments')->latest('sale_date')->take(10),
        ]);

        return view('sppgs.show', compact('sppg'));
    }

    public function edit(Sppg $sppg)
    {
        return view('sppgs.edit', compact('sppg'));
    }

    public function update(Request $request, Sppg $sppg)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:sppgs,code,' . $sppg->id,
            'name' => 'required|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $sppg->update($validated);

        return redirect()->route('sppgs.show', $sppg)
            ->with('success', "Data SPPG {$sppg->name} berhasil diperbarui.");
    }

    public function destroy(Sppg $sppg)
    {
        if ($sppg->sales()->count() > 0 || $sppg->orders()->count() > 0) {
            return back()->with('error', "SPPG {$sppg->name} tidak dapat dihapus karena telah memiliki riwayat pesanan/penjualan. Anda dapat menonaktifkannya.");
        }

        $name = $sppg->name;
        $sppg->delete();

        return redirect()->route('sppgs.index')
            ->with('success', "Data SPPG {$name} berhasil dihapus.");
    }
}
