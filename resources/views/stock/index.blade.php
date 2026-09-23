@extends('layouts.app')

@section('title', 'Manajemen Stok & Gudang — Nutrisaka')
@section('header_title', 'Stok Gudang & Mutasi Pangan')
@section('header_subtitle', 'Pantau ketersediaan bahan pangan segar, peringatan stok menipis, dan riwayat mutasi')

@section('header_actions')
    <a href="{{ route('stock.create-in') }}" class="btn btn-success">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 5v14M5 12h14"/></svg>
        <span>+ Barang Masuk</span>
    </a>
    <a href="{{ route('stock.create-adjustment') }}" class="btn btn-warning">
        <span>Opname Stok</span>
    </a>
@endsection

@section('content')
    <!-- KPI Header -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-navy">
            <div class="kpi-icon navy">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Total Jenis Barang</div>
                <div class="kpi-value">{{ $stats['total_items'] }} <span style="font-size: 14px; font-weight: normal; color: var(--text-sub);">Item</span></div>
                <div class="kpi-subtext">Produk aktif dalam katalog</div>
            </div>
        </div>

        <div class="kpi-card kpi-success">
            <div class="kpi-icon success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Stok Dalam Kondisi Aman</div>
                <div class="kpi-value" style="color: var(--success);">{{ $stats['safe_stock'] }} <span style="font-size: 14px; font-weight: normal; color: var(--text-sub);">Item</span></div>
                <div class="kpi-subtext">Di atas batas minimum</div>
            </div>
        </div>

        <div class="kpi-card kpi-warning">
            <div class="kpi-icon warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Stok Menipis (Kritis)</div>
                <div class="kpi-value" style="color: var(--warning);">{{ $stats['low_stock'] }} <span style="font-size: 14px; font-weight: normal; color: var(--text-sub);">Item</span></div>
                <div class="kpi-subtext">Segera lakukan pengadaan</div>
            </div>
        </div>

        <div class="kpi-card kpi-danger">
            <div class="kpi-icon danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Stok Habis (Kosong)</div>
                <div class="kpi-value" style="color: var(--danger);">{{ $stats['out_of_stock'] }} <span style="font-size: 14px; font-weight: normal; color: var(--text-sub);">Item</span></div>
                <div class="kpi-subtext">Jumlah fisik 0 atau minus</div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="filter-toolbar">
        <form action="{{ route('stock.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; flex-wrap: wrap;">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="form-input" placeholder="Cari nama barang atau SKU..." value="{{ request('search') }}">
            </div>
            <select name="category_id" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="filter" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Kondisi</option>
                <option value="aman" {{ request('filter') === 'aman' ? 'selected' : '' }}>Hanya Stok Aman</option>
                <option value="menipis" {{ request('filter') === 'menipis' ? 'selected' : '' }}>Hanya Stok Menipis</option>
                <option value="habis" {{ request('filter') === 'habis' ? 'selected' : '' }}>Hanya Stok Habis</option>
            </select>
            <button type="submit" class="btn btn-navy">Filter</button>
            @if(request('search') || request('category_id') || request('filter'))
                <a href="{{ route('stock.index') }}" class="btn btn-white">Reset</a>
            @endif
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                Stok Fisik Bahan Pangan Saat Ini ({{ $products->total() }})
            </div>
            <a href="{{ route('stock.history') }}" class="btn btn-sm btn-outline">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Lihat Log Mutasi &rarr;
            </a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Batas Min</th>
                        <th>Stok Fisik Saat Ini</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi Cepat</th>
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
                            </td>
                            <td><span class="badge badge-secondary">{{ $prod->category->name }}</span></td>
                            <td><strong>{{ $prod->unit }}</strong></td>
                            <td>{{ $prod->min_stock }} {{ $prod->unit }}</td>
                            <td>
                                <strong style="font-size: 16px; {{ $prod->current_stock <= $prod->min_stock ? 'color: var(--danger);' : 'color: var(--navy-900);' }}">
                                    {{ $prod->current_stock }}
                                </strong>
                                <span style="font-size: 12px; color: var(--text-sub);">{{ $prod->unit }}</span>
                            </td>
                            <td>
                                @if($prod->stock_status === 'aman')
                                    <span class="badge badge-success">Aman</span>
                                @elseif($prod->stock_status === 'menipis')
                                    <span class="badge badge-warning">Menipis</span>
                                @else
                                    <span class="badge badge-danger">Habis</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('stock.create-in') }}?product_id={{ $prod->id }}" class="btn btn-sm btn-success" title="Tambah barang masuk">
                                    + Masuk
                                </a>
                                <a href="{{ route('stock.create-adjustment') }}?product_id={{ $prod->id }}" class="btn btn-sm btn-white" title="Sesuaikan opname">
                                    Opname
                                </a>
                                <a href="{{ route('stock.history') }}?product_id={{ $prod->id }}" class="btn btn-sm btn-outline" title="Lihat kartu stok">
                                    Kartu Stok
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-light); padding: 40px;">
                                Belum ada produk. Silakan tambahkan produk di menu Katalog Produk.
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
