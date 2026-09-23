@extends('layouts.app')

@section('title', $sppg->name . ' — Nutrisaka')
@section('header_title', $sppg->name)
@section('header_subtitle', 'Profil unit pelanggan SPPG, histori pesanan, faktur penjualan & penagihan piutang')

@section('header_actions')
    <a href="{{ route('orders.create') }}?sppg_id={{ $sppg->id }}" class="btn btn-primary">
        Buat Pesanan
    </a>
    <a href="{{ route('sppgs.edit', $sppg) }}" class="btn btn-white">
        Edit SPPG
    </a>
@endsection

@section('content')
    <!-- Summary Header Cards -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-sky">
            <div class="kpi-icon sky">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Total Belanja SPPG</div>
                <div class="kpi-value">Rp {{ number_format($sppg->total_sales_amount, 0, ',', '.') }}</div>
                <div class="kpi-subtext">Akumulasi seluruh transaksi selesai</div>
            </div>
        </div>

        <div class="kpi-card kpi-success">
            <div class="kpi-icon success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Total Terbayar</div>
                <div class="kpi-value">Rp {{ number_format($sppg->total_paid_amount, 0, ',', '.') }}</div>
                <div class="kpi-subtext">Dana yang telah masuk kas/bank</div>
            </div>
        </div>

        <div class="kpi-card kpi-danger">
            <div class="kpi-icon danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Sisa Piutang Berjalan</div>
                <div class="kpi-value" style="color: var(--danger);">Rp {{ number_format($sppg->total_debt, 0, ',', '.') }}</div>
                <div class="kpi-subtext">Belum dilunasi oleh SPPG</div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 24px; align-items: start;">
        <!-- Left: Detail Profil SPPG -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Informasi Unit SPPG</div>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Kode Unit</div>
                    <div style="font-weight: 700; color: var(--navy-900); font-size: 16px;">{{ $sppg->code }}</div>
                </div>

                <div style="margin-bottom: 16px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Penanggung Jawab (PIC)</div>
                    <div style="font-weight: 600; color: var(--text-main);">{{ $sppg->pic_name ?: '-' }}</div>
                </div>

                <div style="margin-bottom: 16px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Kontak Telepon / WA</div>
                    @if($sppg->phone)
                        <div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sppg->phone) }}" target="_blank" style="color: var(--success); font-weight: 700;">
                                {{ $sppg->phone }} &rarr; Chat WA
                            </a>
                        </div>
                    @else
                        <div style="color: var(--text-light);">-</div>
                    @endif
                </div>

                <div style="margin-bottom: 16px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Alamat Pengiriman</div>
                    <div style="color: var(--text-muted); font-size: 13px;">{{ $sppg->address ?: 'Alamat belum diatur' }}</div>
                </div>

                <div style="margin-bottom: 16px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Catatan / Plafon</div>
                    <div style="color: var(--text-muted); font-size: 13px;">{{ $sppg->notes ?: 'Tidak ada catatan khusus' }}</div>
                </div>

                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Status Kerjasama</div>
                    <div>
                        @if($sppg->is_active)
                            <span class="badge badge-success">Aktif Berjalan</span>
                        @else
                            <span class="badge badge-secondary">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <hr style="border: none; border-top: 1px solid var(--border-light); margin: 16px 0;">

                <form action="{{ route('sppgs.destroy', $sppg) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data SPPG ini? Aksi tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" style="width: 100%;">
                        Hapus Data SPPG
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Riwayat Transaksi & Pesanan -->
        <div>
            <!-- Riwayat Faktur Penjualan -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Riwayat Faktur Penjualan SPPG
                    </div>
                    <a href="{{ route('sales.create') }}?sppg_id={{ $sppg->id }}" class="btn btn-sm btn-outline">+ Jual Cepat</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Invoice</th>
                                <th>Tanggal</th>
                                <th>Total Tagihan</th>
                                <th>Terbayar</th>
                                <th>Sisa Piutang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sppg->sales as $sale)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" style="font-weight: 700; color: var(--sky-600);">
                                            {{ $sale->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                    <td><strong>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong></td>
                                    <td>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($sale->remaining_balance > 0)
                                            <span style="color: var(--danger); font-weight: 700;">Rp {{ number_format($sale->remaining_balance, 0, ',', '.') }}</span>
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
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-white">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--text-light); padding: 25px;">
                                        Belum ada faktur penjualan untuk unit SPPG ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Riwayat Pesanan -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                        Riwayat Pesanan Masuk dari SPPG
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Pesanan</th>
                                <th>Tgl Pesan</th>
                                <th>Tgl Kirim</th>
                                <th>Perkiraan Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sppg->orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" style="font-weight: 700; color: var(--navy-800);">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                    <td>{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</td>
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
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-white">Lihat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-light); padding: 25px;">
                                        Belum ada riwayat pesanan dari SPPG ini.
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
