@extends('layouts.app')

@section('title', 'Daftar Pesanan SPPG — Nutrisaka')
@section('header_title', 'Pesanan Masuk dari SPPG')
@section('header_subtitle', 'Kelola permintaan bahan makanan dari SPPG sebelum diproses menjadi penjualan')

@section('header_actions')
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Input Pesanan Baru</span>
    </a>
@endsection

@section('content')
    <!-- Status Tabs -->
    <div class="tabs-nav">
        <a href="{{ route('orders.index') }}" class="tab-link {{ !request('status') ? 'active' : '' }}">
            Semua Pesanan <span class="badge badge-secondary">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('orders.index', ['status' => 'menunggu']) }}" class="tab-link {{ request('status') === 'menunggu' ? 'active' : '' }}">
            Menunggu <span class="badge badge-warning">{{ $counts['menunggu'] }}</span>
        </a>
        <a href="{{ route('orders.index', ['status' => 'diproses']) }}" class="tab-link {{ request('status') === 'diproses' ? 'active' : '' }}">
            Sedang Diproses <span class="badge badge-info">{{ $counts['diproses'] }}</span>
        </a>
        <a href="{{ route('orders.index', ['status' => 'selesai']) }}" class="tab-link {{ request('status') === 'selesai' ? 'active' : '' }}">
            Selesai (Terjual) <span class="badge badge-success">{{ $counts['selesai'] }}</span>
        </a>
        <a href="{{ route('orders.index', ['status' => 'dibatalkan']) }}" class="tab-link {{ request('status') === 'dibatalkan' ? 'active' : '' }}">
            Dibatalkan <span class="badge badge-danger">{{ $counts['dibatalkan'] }}</span>
        </a>
    </div>

    <!-- Search Toolbar -->
    <div class="filter-toolbar">
        <form action="{{ route('orders.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; flex-wrap: wrap;">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="form-input" placeholder="Cari nomor pesanan atau SPPG..." value="{{ request('search') }}">
            </div>
            <select name="sppg_id" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Pelanggan SPPG</option>
                @foreach($sppgs as $sp)
                    <option value="{{ $sp->id }}" {{ request('sppg_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-navy">Filter</button>
            @if(request('search') || request('sppg_id'))
                <a href="{{ route('orders.index', request('status') ? ['status' => request('status')] : []) }}" class="btn btn-white">Reset</a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                Daftar Pesanan ({{ $orders->total() }})
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan SPPG</th>
                        <th>Tgl Pesan</th>
                        <th>Tgl Kirim</th>
                        <th>Jumlah Item</th>
                        <th>Perkiraan Total (Rp)</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" style="font-weight: 700; color: var(--navy-800); font-size: 15px;">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <strong>{{ $order->sppg->name }}</strong>
                                <div style="font-size: 12px; color: var(--text-sub);">PIC: {{ $order->sppg->pic_name ?: '-' }}</div>
                            </td>
                            <td>{{ $order->order_date->format('d/m/Y') }}</td>
                            <td>
                                @if($order->delivery_date)
                                    <span style="font-weight: 600;">{{ $order->delivery_date->format('d/m/Y') }}</span>
                                @else
                                    <span style="color: var(--text-light);">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $order->items->count() }} Jenis Produk</span>
                            </td>
                            <td>
                                <strong style="font-size: 15px; color: var(--sky-600);">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                @if($order->status === 'selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($order->status === 'diproses')
                                    <span class="badge badge-info">Diproses</span>
                                @elseif($order->status === 'menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($order->status === 'dibatalkan')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @else
                                    <span class="badge badge-secondary">Draft</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">
                                    Proses / Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-light); padding: 40px;">
                                Belum ada pesanan dalam kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 20px;">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
