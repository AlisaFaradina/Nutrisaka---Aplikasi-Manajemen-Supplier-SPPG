@extends('layouts.app')

@section('title', 'Dashboard — Nutrisaka Supplier SPPG')
@section('header_title', 'Dashboard Supplier')
@section('header_subtitle', 'Ringkasan operasional pasokan bahan pangan ke seluruh SPPG binaan')

@section('header_actions')
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        <span>+ Pesanan Baru</span>
    </a>
@endsection

@section('content')
    <!-- Quick Actions Strip -->
    <div class="quick-actions-card">
        <div class="quick-actions-text">
            <h3>Aksi Cepat Operasional Supplier</h3>
            <p>Pilih tindakan yang sering digunakan untuk memproses pesanan dan mencatat pasokan</p>
        </div>
        <div class="quick-actions-btns">
            <a href="{{ route('orders.create') }}" class="btn btn-white">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                Input Pesanan SPPG
            </a>
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Penjualan Langsung (Kasir)
            </a>
            <a href="{{ route('stock.create-in') }}" class="btn btn-success">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 5v14M5 12h14"/></svg>
                Catat Barang Masuk (+)
            </a>
            <a href="{{ route('payments.index') }}" class="btn btn-warning">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Tagih Piutang SPPG
            </a>
        </div>
    </div>

    <!-- 5 KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-sky">
            <div class="kpi-icon sky">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Penjualan Hari Ini</div>
                <div class="kpi-value">Rp {{ number_format($salesToday, 0, ',', '.') }}</div>
                <div class="kpi-subtext">Total transaksi final hari ini</div>
            </div>
        </div>

        <div class="kpi-card kpi-success">
            <div class="kpi-icon success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Penjualan Bulan Ini</div>
                <div class="kpi-value">Rp {{ number_format($salesThisMonth, 0, ',', '.') }}</div>
                <div class="kpi-subtext">Akumulasi {{ now()->translatedFormat('F Y') }}</div>
            </div>
        </div>

        <div class="kpi-card kpi-warning">
            <div class="kpi-icon warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Pesanan Diproses</div>
                <div class="kpi-value">{{ $pendingOrdersCount }} <span style="font-size: 14px; font-weight: normal; color: var(--text-sub);">Pesanan</span></div>
                <div class="kpi-subtext">Menunggu disiapkan / dikirim</div>
            </div>
        </div>

        <div class="kpi-card kpi-danger">
            <div class="kpi-icon danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Total Piutang SPPG</div>
                <div class="kpi-value">Rp {{ number_format($totalDebt, 0, ',', '.') }}</div>
                <div class="kpi-subtext">Belum tertagih / cicilan</div>
            </div>
        </div>

        <div class="kpi-card kpi-navy">
            <div class="kpi-icon navy">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Pelanggan SPPG</div>
                <div class="kpi-value">{{ $activeSppgCount }} <span style="font-size: 14px; font-weight: normal; color: var(--text-sub);">Unit</span></div>
                <div class="kpi-subtext">SPPG aktif dilayani</div>
            </div>
        </div>
    </div>

    <!-- Main Content 2 Columns -->
    <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 24px;">
        <!-- Left Column: Sales 7 Days + Recent Orders -->
        <div>
            <!-- Penjualan 7 Hari Terakhir -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        Tren Penjualan 7 Hari Terakhir
                    </div>
                    <span style="font-size: 12px; color: var(--text-sub);">Grafik harian</span>
                </div>
                <div class="card-body">
                    @php
                        $maxSale = max(array_column($last7Days, 'amount')) ?: 1;
                    @endphp
                    <div class="chart-container">
                        @foreach($last7Days as $day)
                            @php
                                $heightPct = min(100, max(8, round(($day['amount'] / $maxSale) * 100)));
                            @endphp
                            <div class="chart-bar-wrap">
                                @if($day['amount'] > 0)
                                    <div class="chart-amount">{{ number_format($day['amount']/1000, 0) }}k</div>
                                @else
                                    <div class="chart-amount" style="color: var(--text-light);">-</div>
                                @endif
                                <div class="chart-bar" style="height: {{ $heightPct }}%;" title="{{ $day['label'] }}: Rp {{ number_format($day['amount'], 0, ',', '.') }}"></div>
                                <div class="chart-label">{{ $day['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Pesanan Terbaru -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                        Pesanan SPPG Terbaru
                    </div>
                    <a href="{{ route('orders.index') }}" style="font-size: 13px; font-weight: 600;">Lihat Semua &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Pelanggan SPPG</th>
                                <th>Tgl Pesan</th>
                                <th>Perkiraan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" style="font-weight: 700; color: var(--navy-800);">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <strong>{{ $order->sppg->name }}</strong>
                                        <div style="font-size: 12px; color: var(--text-sub);">{{ $order->sppg->pic_name ?? '-' }}</div>
                                    </td>
                                    <td>{{ $order->order_date->format('d M Y') }}</td>
                                    <td><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                                    <td>
                                        @if($order->status === 'selesai')
                                            <span class="badge badge-success">Selesai</span>
                                        @elseif($order->status === 'diproses')
                                            <span class="badge badge-info">Diproses</span>
                                        @elseif($order->status === 'menunggu')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @elseif($order->status === 'dibatalkan')
                                            <span class="badge badge-danger">Batal</span>
                                        @else
                                            <span class="badge badge-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-light); padding: 30px;">
                                        Belum ada pesanan masuk. Klik <strong>+ Pesanan Baru</strong> untuk mulai mencatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Low Stock Alerts + Recent Sales -->
        <div>
            <!-- Peringatan Stok Menipis -->
            <div class="card">
                <div class="card-header" style="background: var(--warning-bg); border-bottom-color: var(--warning-border);">
                    <div class="card-title" style="color: var(--warning);">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Peringatan Stok Menipis ({{ $lowStockProducts->count() }})
                    </div>
                    <a href="{{ route('stock.create-in') }}" class="btn btn-sm btn-warning">Tambah Stok</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            @forelse($lowStockProducts as $p)
                                <tr>
                                    <td>
                                        <strong>{{ $p->name }}</strong>
                                        <div style="font-size: 11px; color: var(--text-sub);">SKU: {{ $p->sku }} • {{ $p->category->name }}</div>
                                    </td>
                                    <td style="text-align: right;">
                                        @if($p->current_stock <= 0)
                                            <span class="badge badge-danger">Habis (0 {{ $p->unit }})</span>
                                        @else
                                            <span class="badge badge-warning">{{ $p->current_stock }} {{ $p->unit }}</span>
                                        @endif
                                        <div style="font-size: 11px; color: var(--text-light);">Min: {{ $p->min_stock }} {{ $p->unit }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" style="text-align: center; color: var(--success); padding: 20px;">
                                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 4px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        <div>Seluruh stok produk dalam kondisi aman!</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Penjualan Terakhir -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Faktur Penjualan Terakhir
                    </div>
                    <a href="{{ route('sales.index') }}" style="font-size: 13px; font-weight: 600;">Semua &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" style="font-weight: 700; color: var(--sky-600);">
                                            {{ $sale->invoice_number }}
                                        </a>
                                        <div style="font-size: 12px; color: var(--text-sub);">{{ $sale->sppg->name }}</div>
                                    </td>
                                    <td style="text-align: right;">
                                        <div><strong>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong></div>
                                        @if($sale->payment_status === 'lunas')
                                            <span class="badge badge-success">Lunas</span>
                                        @elseif($sale->payment_status === 'sebagian')
                                            <span class="badge badge-warning">Sisa: Rp {{ number_format($sale->remaining_balance, 0, ',', '.') }}</span>
                                        @else
                                            <span class="badge badge-danger">Belum Bayar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" style="text-align: center; color: var(--text-light); padding: 25px;">
                                        Belum ada data penjualan tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
