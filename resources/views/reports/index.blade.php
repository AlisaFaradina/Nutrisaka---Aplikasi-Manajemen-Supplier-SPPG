@extends('layouts.app')

@section('title', 'Laporan Terpadu — Nutrisaka')
@section('header_title', 'Pusat Laporan Supplier SPPG')
@section('header_subtitle', 'Analisis penjualan, performa unit SPPG, perputaran produk pangan, dan piutang')

@section('header_actions')
    <button onclick="window.print()" class="btn btn-navy">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak Laporan
    </button>
    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-success">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Ekspor Excel / CSV
    </a>
@endsection

@section('content')
    <!-- Report Type Tabs -->
    <div class="tabs-nav no-print">
        <a href="{{ route('reports.index', ['type' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="tab-link {{ $type === 'sales' ? 'active' : '' }}">
            Laporan Penjualan (Faktur)
        </a>
        <a href="{{ route('reports.index', ['type' => 'sppg', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="tab-link {{ $type === 'sppg' ? 'active' : '' }}">
            Penjualan per SPPG
        </a>
        <a href="{{ route('reports.index', ['type' => 'products', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="tab-link {{ $type === 'products' ? 'active' : '' }}">
            Penjualan per Produk
        </a>
        <a href="{{ route('reports.index', ['type' => 'debts', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="tab-link {{ $type === 'debts' ? 'active' : '' }}">
            Laporan Tagihan & Piutang
        </a>
        <a href="{{ route('reports.index', ['type' => 'stock', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="tab-link {{ $type === 'stock' ? 'active' : '' }}">
            Laporan Mutasi Stok
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card no-print">
        <div class="card-body" style="padding: 16px 20px;">
            <form action="{{ route('reports.index') }}" method="GET" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
                <input type="hidden" name="type" value="{{ $type }}">
                <div style="flex: 1; min-width: 170px;">
                    <label class="form-label" style="font-size: 12px;">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
                </div>
                <div style="flex: 1; min-width: 170px;">
                    <label class="form-label" style="font-size: 12px;">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
                </div>
                @if($type === 'sales')
                    <div style="flex: 1.2; min-width: 200px;">
                        <label class="form-label" style="font-size: 12px;">Filter Pelanggan SPPG</label>
                        <select name="sppg_id" class="form-select">
                            <option value="">Semua Pelanggan SPPG</option>
                            @foreach($sppgs as $sp)
                                <option value="{{ $sp->id }}" {{ $sppgId == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <button type="submit" class="btn btn-navy">Tampilkan Laporan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Printable Header Banner -->
    <div class="print-only" style="margin-bottom: 20px; border-bottom: 2px solid #0f1e36; padding-bottom: 12px;">
        <h2 style="font-size: 20px; color: #0c192c;">{{ $settings['supplier_name'] }}</h2>
        <div style="font-size: 13px; color: #475569;">{{ $settings['supplier_tagline'] }} • Telp: {{ $settings['supplier_phone'] }}</div>
        <div style="font-size: 15px; font-weight: bold; margin-top: 8px; text-transform: uppercase;">
            @if($type === 'sales') Laporan Penjualan Faktur
            @elseif($type === 'sppg') Laporan Penjualan per Unit Pelanggan SPPG
            @elseif($type === 'products') Laporan Penjualan & Perputaran Produk Pangan
            @elseif($type === 'debts') Laporan Rekapitulasi Piutang SPPG Belum Lunas
            @else Laporan Pergerakan & Mutasi Stok Gudang
            @endif
        </div>
        <div style="font-size: 12px; color: #64748b;">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}</div>
    </div>

    <!-- REPORT CONTENT BASED ON TYPE -->
    @if($type === 'sales')
        <!-- Summary KPI -->
        <div class="kpi-grid">
            <div class="kpi-card kpi-navy">
                <div class="kpi-info">
                    <div class="kpi-title">Total Transaksi</div>
                    <div class="kpi-value">{{ $data['summary']['total_transactions'] }} <span style="font-size: 14px; font-weight: normal; color: var(--text-sub);">Faktur</span></div>
                </div>
            </div>
            <div class="kpi-card kpi-sky">
                <div class="kpi-info">
                    <div class="kpi-title">Total Omset Penjualan</div>
                    <div class="kpi-value">Rp {{ number_format($data['summary']['total_revenue'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="kpi-card kpi-success">
                <div class="kpi-info">
                    <div class="kpi-title">Total Pembayaran Masuk</div>
                    <div class="kpi-value">Rp {{ number_format($data['summary']['total_paid'], 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="kpi-card kpi-danger">
                <div class="kpi-info">
                    <div class="kpi-title">Total Sisa Piutang</div>
                    <div class="kpi-value" style="color: var(--danger);">Rp {{ number_format($data['summary']['total_debt'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Faktur</th>
                            <th>Tanggal</th>
                            <th>Pelanggan SPPG</th>
                            <th style="text-align: right;">Subtotal</th>
                            <th style="text-align: right;">Diskon</th>
                            <th style="text-align: right;">Total Akhir</th>
                            <th style="text-align: right;">Terbayar</th>
                            <th style="text-align: right;">Sisa Piutang</th>
                            <th>Status Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['sales'] as $s)
                            <tr>
                                <td><strong>{{ $s->invoice_number }}</strong></td>
                                <td>{{ $s->sale_date->format('d/m/Y') }}</td>
                                <td>{{ $s->sppg->name }}</td>
                                <td style="text-align: right;">Rp {{ number_format($s->subtotal, 0, ',', '.') }}</td>
                                <td style="text-align: right;">Rp {{ number_format($s->discount, 0, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: 700;">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                                <td style="text-align: right; color: var(--success);">Rp {{ number_format($s->paid_amount, 0, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: 700; color: {{ $s->remaining_balance > 0 ? 'var(--danger)' : 'var(--success)' }};">
                                    Rp {{ number_format($s->remaining_balance, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($s->payment_status === 'lunas')
                                        <span class="badge badge-success">Lunas</span>
                                    @elseif($s->payment_status === 'sebagian')
                                        <span class="badge badge-warning">Sebagian</span>
                                    @else
                                        <span class="badge badge-danger">Belum Bayar</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; color: var(--text-light); padding: 40px;">
                                    Tidak ada transaksi penjualan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($type === 'sppg')
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama SPPG</th>
                            <th>PIC / Penanggung Jawab</th>
                            <th>Kontak</th>
                            <th style="text-align: center;">Jumlah Transaksi</th>
                            <th style="text-align: right;">Total Belanja</th>
                            <th style="text-align: right;">Telah Dibayar</th>
                            <th style="text-align: right;">Sisa Piutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['items'] as $it)
                            <tr>
                                <td><strong>{{ $it['code'] }}</strong></td>
                                <td><strong>{{ $it['name'] }}</strong></td>
                                <td>{{ $it['pic_name'] ?: '-' }}</td>
                                <td>{{ $it['phone'] ?: '-' }}</td>
                                <td style="text-align: center;">{{ $it['sales_count'] }} kali</td>
                                <td style="text-align: right; font-weight: 700; color: var(--navy-900);">Rp {{ number_format($it['total_amount'], 0, ',', '.') }}</td>
                                <td style="text-align: right; color: var(--success);">Rp {{ number_format($it['total_paid'], 0, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: 700; color: {{ $it['total_debt'] > 0 ? 'var(--danger)' : 'var(--success)' }};">
                                    Rp {{ number_format($it['total_debt'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-light); padding: 40px;">
                                    Belum ada data transaksi per SPPG pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($type === 'products')
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode SKU</th>
                            <th>Nama Produk Pangan</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th style="text-align: right;">Total Kuantitas Terjual</th>
                            <th style="text-align: right;">Total Omset (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['items'] as $prod)
                            <tr>
                                <td><strong>{{ $prod['sku'] }}</strong></td>
                                <td><strong>{{ $prod['product_name'] }}</strong></td>
                                <td><span class="badge badge-secondary">{{ $prod['category'] }}</span></td>
                                <td>{{ $prod['unit'] }}</td>
                                <td style="text-align: right; font-weight: 700; font-size: 15px;">{{ $prod['total_qty'] }} {{ $prod['unit'] }}</td>
                                <td style="text-align: right; font-weight: 800; color: var(--sky-600); font-size: 15px;">
                                    Rp {{ number_format($prod['total_amount'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-light); padding: 40px;">
                                    Belum ada penjualan produk pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($type === 'debts')
        <div class="card">
            <div class="card-header">
                <div class="card-title">Total Piutang Belum Lunas: Rp {{ number_format($data['total_debt'], 0, ',', '.') }}</div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Faktur</th>
                            <th>Tanggal Penjualan</th>
                            <th>Jatuh Tempo</th>
                            <th>Pelanggan SPPG</th>
                            <th style="text-align: right;">Total Tagihan</th>
                            <th style="text-align: right;">Telah Dibayar</th>
                            <th style="text-align: right;">Sisa Piutang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['sales'] as $s)
                            <tr>
                                <td><strong>{{ $s->invoice_number }}</strong></td>
                                <td>{{ $s->sale_date->format('d/m/Y') }}</td>
                                <td>
                                    <span style="{{ $s->due_date && now()->gt($s->due_date) ? 'color: var(--danger); font-weight: bold;' : '' }}">
                                        {{ $s->due_date ? $s->due_date->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td><strong>{{ $s->sppg->name }}</strong> ({{ $s->sppg->phone ?: '-' }})</td>
                                <td style="text-align: right;">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                                <td style="text-align: right; color: var(--success);">Rp {{ number_format($s->paid_amount, 0, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: 800; color: var(--danger); font-size: 15px;">
                                    Rp {{ number_format($s->remaining_balance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--success); padding: 40px;">
                                    Tidak ada piutang yang beredar saat ini. Seluruh tagihan telah lunas!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($type === 'stock')
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu Mutasi</th>
                            <th>Nama Produk</th>
                            <th>Jenis</th>
                            <th style="text-align: right;">Kuantitas</th>
                            <th style="text-align: center;">Perubahan Saldo</th>
                            <th>Keterangan / Referensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['movements'] as $m)
                            <tr>
                                <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td><strong>{{ $m->product?->name }}</strong></td>
                                <td>
                                    @if($m->type === 'in')
                                        <span class="badge badge-success">+ MASUK</span>
                                    @elseif($m->type === 'out')
                                        <span class="badge badge-danger">- KELUAR</span>
                                    @else
                                        <span class="badge badge-warning">OPNAME</span>
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: 700;">{{ $m->quantity }} {{ $m->product?->unit }}</td>
                                <td style="text-align: center;">{{ $m->before_stock }} &rarr; {{ $m->after_stock }}</td>
                                <td>{{ $m->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-light); padding: 40px;">
                                    Tidak ada mutasi stok tercatat pada rentang waktu ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
