@extends('layouts.app')

@section('title', 'Riwayat Penjualan & Faktur — Nutrisaka')
@section('header_title', 'Faktur Penjualan SPPG')
@section('header_subtitle', 'Kelola transaksi penjualan, status invoice, dan penagihan piutang SPPG')

@section('header_actions')
    <a href="{{ route('sales.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span>+ Jual Langsung (Kasir)</span>
    </a>
@endsection

@section('content')
    <!-- Status Tabs -->
    <div class="tabs-nav">
        <a href="{{ route('sales.index') }}" class="tab-link {{ !request('payment_status') ? 'active' : '' }}">
            Semua Faktur <span class="badge badge-secondary">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('sales.index', ['payment_status' => 'belum_bayar']) }}" class="tab-link {{ request('payment_status') === 'belum_bayar' ? 'active' : '' }}">
            Belum Bayar <span class="badge badge-danger">{{ $counts['belum_bayar'] }}</span>
        </a>
        <a href="{{ route('sales.index', ['payment_status' => 'sebagian']) }}" class="tab-link {{ request('payment_status') === 'sebagian' ? 'active' : '' }}">
            Sebagian (Piutang) <span class="badge badge-warning">{{ $counts['sebagian'] }}</span>
        </a>
        <a href="{{ route('sales.index', ['payment_status' => 'lunas']) }}" class="tab-link {{ request('payment_status') === 'lunas' ? 'active' : '' }}">
            Lunas <span class="badge badge-success">{{ $counts['lunas'] }}</span>
        </a>
    </div>

    <!-- Search Toolbar -->
    <div class="filter-toolbar">
        <form action="{{ route('sales.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; flex-wrap: wrap;">
            @if(request('payment_status'))
                <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
            @endif
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="form-input" placeholder="Cari nomor invoice atau nama SPPG..." value="{{ request('search') }}">
            </div>
            <select name="sppg_id" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua Pelanggan SPPG</option>
                @foreach($sppgs as $sp)
                    <option value="{{ $sp->id }}" {{ request('sppg_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-navy">Filter</button>
            @if(request('search') || request('sppg_id'))
                <a href="{{ route('sales.index', request('payment_status') ? ['payment_status' => request('payment_status')] : []) }}" class="btn btn-white">Reset</a>
            @endif
        </form>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Daftar Faktur Penjualan ({{ $sales->total() }})
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tgl Penjualan</th>
                        <th>Pelanggan SPPG</th>
                        <th>Total Tagihan (Rp)</th>
                        <th>Sudah Dibayar</th>
                        <th>Sisa Piutang</th>
                        <th>Status Bayar</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr style="{{ $sale->status === 'batal' ? 'opacity: 0.6; background: #fff5f5;' : '' }}">
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" style="font-weight: 700; color: var(--sky-600); font-size: 15px;">
                                    {{ $sale->invoice_number }}
                                </a>
                                @if($sale->status === 'batal')
                                    <span class="badge badge-danger" style="margin-left: 5px;">BATAL</span>
                                @endif
                                @if($sale->order)
                                    <div style="font-size: 11px; color: var(--text-sub);">Dari Pesanan: {{ $sale->order->order_number }}</div>
                                @endif
                            </td>
                            <td>
                                {{ $sale->sale_date->format('d/m/Y') }}
                                @if($sale->due_date && $sale->remaining_balance > 0)
                                    <div style="font-size: 11px; color: var(--danger);">Tempo: {{ $sale->due_date->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $sale->sppg->name }}</strong>
                            </td>
                            <td>
                                <strong style="font-size: 15px; color: var(--navy-900);">
                                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                <span style="color: var(--success); font-weight: 600;">
                                    Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @if($sale->remaining_balance > 0)
                                    <span style="color: var(--danger); font-weight: 700;">
                                        Rp {{ number_format($sale->remaining_balance, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span style="color: var(--success); font-weight: 600;">Rp 0</span>
                                @endif
                            </td>
                            <td>
                                @if($sale->payment_status === 'lunas')
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($sale->payment_status === 'sebagian')
                                    <span class="badge badge-warning">Sebagian</span>
                                @else
                                    <span class="badge badge-danger">Belum Bayar</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-primary">Faktur</a>
                                <a href="{{ route('sales.print-thermal', $sale) }}" target="_blank" class="btn btn-sm btn-white" title="Cetak Struk Kasir Thermal">Struk</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-light); padding: 40px;">
                                Belum ada transaksi penjualan dalam kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding: 16px 20px;">
            {{ $sales->links() }}
        </div>
    </div>
@endsection
