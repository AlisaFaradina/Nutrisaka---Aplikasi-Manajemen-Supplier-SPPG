@extends('layouts.app')

@section('title', 'Histori Mutasi Stok — Nutrisaka')
@section('header_title', 'Histori & Audit Mutasi Stok')
@section('header_subtitle', 'Catatan lengkap seluruh pergerakan barang masuk, keluar penjualan, dan penyesuaian opname')

@section('header_actions')
    <a href="{{ route('stock.index') }}" class="btn btn-white">&larr; Kembali ke Gudang</a>
@endsection

@section('content')
    <!-- Filter Toolbar -->
    <div class="filter-toolbar">
        <form action="{{ route('stock.history') }}" method="GET" style="display: flex; gap: 10px; flex: 1; flex-wrap: wrap;">
            <select name="product_id" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Produk</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
            <select name="type" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Jenis Mutasi</option>
                <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Stok Masuk (+)</option>
                <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Stok Keluar Penjualan (-)</option>
                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian Opname</option>
            </select>
            <input type="date" name="start_date" class="form-input" style="width: auto;" value="{{ request('start_date') }}" title="Dari Tanggal">
            <input type="date" name="end_date" class="form-input" style="width: auto;" value="{{ request('end_date') }}" title="Sampai Tanggal">
            <button type="submit" class="btn btn-navy">Filter</button>
            @if(request('product_id') || request('type') || request('start_date') || request('end_date'))
                <a href="{{ route('stock.history') }}" class="btn btn-white">Reset</a>
            @endif
        </form>
    </div>

    <!-- Movements Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Log Mutasi Stok Terdaftar ({{ $movements->total() }})
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu Mutasi</th>
                        <th>Nama Produk Pangan</th>
                        <th>Jenis Mutasi</th>
                        <th style="text-align: right;">Jumlah Perubahan</th>
                        <th style="text-align: center;">Perubahan Saldo</th>
                        <th>Keterangan / Referensi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        <tr>
                            <td>
                                <strong>{{ $m->created_at->format('d/m/Y') }}</strong>
                                <div style="font-size: 11px; color: var(--text-sub);">Pukul {{ $m->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                <strong style="color: var(--navy-900);">{{ $m->product?->name ?: 'Produk Terhapus' }}</strong>
                                <div style="font-size: 11px; color: var(--text-sub);">SKU: {{ $m->product?->sku ?? '-' }}</div>
                            </td>
                            <td>
                                @if($m->type === 'in')
                                    <span class="badge badge-success">+ MASUK</span>
                                @elseif($m->type === 'out')
                                    <span class="badge badge-danger">- KELUAR</span>
                                @else
                                    <span class="badge badge-warning">OPNAME</span>
                                @endif
                            </td>
                            <td style="text-align: right; font-weight: 700; font-size: 15px;">
                                @if($m->type === 'in')
                                    <span style="color: var(--success);">+{{ $m->quantity }} {{ $m->product?->unit }}</span>
                                @elseif($m->type === 'out')
                                    <span style="color: var(--danger);">-{{ $m->quantity }} {{ $m->product?->unit }}</span>
                                @else
                                    <span>{{ $m->quantity }} {{ $m->product?->unit }}</span>
                                @endif
                            </td>
                            <td style="text-align: center; font-size: 13px;">
                                <span style="color: var(--text-sub);">{{ $m->before_stock }}</span>
                                <span style="color: var(--sky-600); margin: 0 4px;">&rarr;</span>
                                <strong style="color: var(--navy-900);">{{ $m->after_stock }} {{ $m->product?->unit }}</strong>
                            </td>
                            <td>
                                <div style="font-size: 13px; color: var(--text-main);">{{ $m->notes }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-light); padding: 40px;">
                                Belum ada riwayat mutasi tercatat sesuai filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 20px;">
            {{ $movements->links() }}
        </div>
    </div>
@endsection
