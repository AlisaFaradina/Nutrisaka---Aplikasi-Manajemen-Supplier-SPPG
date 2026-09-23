@extends('layouts.app')

@section('title', 'Katalog Produk — Nutrisaka')
@section('header_title', 'Katalog Produk & Harga')
@section('header_subtitle', 'Kelola daftar barang pangan, harga jual satuan, dan batas stok minimum')

@section('header_actions')
    <a href="{{ route('categories.index') }}" class="btn btn-white">
        Kelola Kategori
    </a>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>+ Tambah Produk</span>
    </a>
@endsection

@section('content')
    <!-- Filter Bar -->
    <div class="filter-toolbar">
        <form action="{{ route('products.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; flex-wrap: wrap;">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="form-input" placeholder="Cari nama produk atau SKU..." value="{{ request('search') }}">
            </div>
            <select name="category_id" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="stock_status" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Kondisi Stok</option>
                <option value="aman" {{ request('stock_status') === 'aman' ? 'selected' : '' }}>Stok Aman</option>
                <option value="menipis" {{ request('stock_status') === 'menipis' ? 'selected' : '' }}>Stok Menipis (&le; Min)</option>
                <option value="habis" {{ request('stock_status') === 'habis' ? 'selected' : '' }}>Stok Habis (0)</option>
            </select>
            <button type="submit" class="btn btn-navy">Filter</button>
            @if(request('search') || request('category_id') || request('stock_status'))
                <a href="{{ route('products.index') }}" class="btn btn-white">Reset</a>
            @endif
        </form>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                Daftar Produk Bahan Pangan SPPG ({{ $products->total() }})
            </div>
            <div style="display: flex; gap: 12px; font-size: 13px;">
                <span style="display: flex; align-items: center; gap: 5px;"><span class="badge badge-success" style="padding: 2px 6px;">●</span> Aman</span>
                <span style="display: flex; align-items: center; gap: 5px;"><span class="badge badge-warning" style="padding: 2px 6px;">●</span> Menipis</span>
                <span style="display: flex; align-items: center; gap: 5px;"><span class="badge badge-danger" style="padding: 2px 6px;">●</span> Habis</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Harga Jual (Rp)</th>
                        <th>Stok Saat Ini</th>
                        <th>Status Stok</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $prod)
                        <tr>
                            <td>
                                <span style="font-weight: 700; color: var(--navy-800); background: var(--bg-alt); padding: 3px 8px; border-radius: var(--radius-sm); font-size: 12px;">
                                    {{ $prod->sku }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 15px; color: var(--navy-900);">{{ $prod->name }}</strong>
                                @if($prod->description)
                                    <div style="font-size: 12px; color: var(--text-sub);">{{ $prod->description }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $prod->category->name }}</span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--text-main);">{{ $prod->unit }}</span>
                            </td>
                            <td>
                                <strong style="font-size: 15px; color: var(--sky-600);">
                                    Rp {{ number_format($prod->selling_price, 0, ',', '.') }}
                                </strong>
                                @if($prod->cost_price > 0)
                                    <div style="font-size: 11px; color: var(--text-light);">Beli: Rp {{ number_format($prod->cost_price, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td>
                                <strong style="font-size: 15px;">{{ $prod->current_stock }}</strong>
                                <span style="font-size: 12px; color: var(--text-sub);">{{ $prod->unit }}</span>
                                <div style="font-size: 11px; color: var(--text-light);">Min: {{ $prod->min_stock }}</div>
                            </td>
                            <td>
                                @if($prod->stock_status === 'aman')
                                    <span class="badge badge-success">Stok Aman</span>
                                @elseif($prod->stock_status === 'menipis')
                                    <span class="badge badge-warning">Stok Menipis</span>
                                @else
                                    <span class="badge badge-danger">Stok Habis</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('products.edit', $prod) }}" class="btn btn-sm btn-white">Edit</a>
                                <form action="{{ route('products.destroy', $prod) }}" method="POST" onsubmit="return confirm('Hapus produk {{ $prod->name }}?')" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-light); padding: 40px;">
                                Belum ada produk. Klik tombol <strong>+ Tambah Produk</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 20px;">
            {{ $products->links() }}
        </div>
    </div>
@endsection
