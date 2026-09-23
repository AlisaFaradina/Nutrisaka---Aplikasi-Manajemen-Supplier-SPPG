@extends('layouts.app')

@section('title', 'Faktur ' . $sale->invoice_number . ' — Nutrisaka')
@section('header_title', 'Faktur Penjualan: ' . $sale->invoice_number)
@section('header_subtitle', 'Transaksi pasokan ke ' . $sale->sppg->name)

@section('header_actions')
    <a href="{{ route('sales.index') }}" class="btn btn-white">&larr; Riwayat Penjualan</a>
    <a href="{{ route('sales.print-invoice', $sale) }}" target="_blank" class="btn btn-navy">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak Faktur A4
    </a>
    <a href="{{ route('sales.print-thermal', $sale) }}" target="_blank" class="btn btn-white" title="Ukuran Struk Thermal 58mm/80mm">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg>
        Cetak Struk Kasir
    </a>
@endsection

@section('content')
@if($sale->status === 'batal')
    <div class="alert alert-error">
        <strong>PERINGATAN: Faktur Penjualan Ini Telah Dibatalkan!</strong>
        <p style="margin-top: 4px;">{{ $sale->notes }}</p>
    </div>
@endif

<!-- Summary KPI Header -->
<div class="kpi-grid">
    <div class="kpi-card kpi-sky">
        <div class="kpi-icon sky">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-title">Total Tagihan Faktur</div>
            <div class="kpi-value">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</div>
            <div class="kpi-subtext">Setelah dikurangi potongan harga</div>
        </div>
    </div>

    <div class="kpi-card kpi-success">
        <div class="kpi-icon success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-title">Total Telah Dibayar</div>
            <div class="kpi-value">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</div>
            <div class="kpi-subtext">Akumulasi seluruh pembayaran</div>
        </div>
    </div>

    <div class="kpi-card {{ $sale->remaining_balance > 0 ? 'kpi-danger' : 'kpi-success' }}">
        <div class="kpi-icon {{ $sale->remaining_balance > 0 ? 'danger' : 'success' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="kpi-info">
            <div class="kpi-title">Sisa Piutang</div>
            <div class="kpi-value" style="{{ $sale->remaining_balance > 0 ? 'color: var(--danger);' : 'color: var(--success);' }}">
                Rp {{ number_format($sale->remaining_balance, 0, ',', '.') }}
            </div>
            <div class="kpi-subtext">
                @if($sale->payment_status === 'lunas')
                    <span class="badge badge-success">LUNAS</span>
                @elseif($sale->payment_status === 'sebagian')
                    <span class="badge badge-warning">SEBAGIAN</span>
                @else
                    <span class="badge badge-danger">BELUM BAYAR</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">
    <!-- Left Column: Item List & Payment History -->
    <div>
        <!-- Rincian Produk Terjual -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    Daftar Barang Terjual ({{ $sale->items->count() }} Item)
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk Bahan Pangan</th>
                            <th>Kategori</th>
                            <th style="text-align: right;">Kuantitas</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong style="color: var(--navy-900);">{{ $item->product->name }}</strong>
                                    <div style="font-size: 11px; color: var(--text-sub);">SKU: {{ $item->product->sku }}</div>
                                </td>
                                <td><span class="badge badge-secondary">{{ $item->product->category->name }}</span></td>
                                <td style="text-align: right; font-weight: 700;">
                                    {{ $item->quantity }} {{ $item->product->unit }}
                                </td>
                                <td style="text-align: right;">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; font-weight: 700; color: var(--navy-900);">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align: right; font-weight: 600;">Subtotal Barang:</td>
                            <td style="text-align: right; font-weight: 700;">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @if($sale->discount > 0)
                            <tr>
                                <td colspan="5" style="text-align: right; color: var(--danger); font-weight: 600;">Potongan Harga (Diskon):</td>
                                <td style="text-align: right; color: var(--danger); font-weight: 700;">- Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr style="background: var(--bg-alt); font-size: 16px;">
                            <td colspan="5" style="text-align: right; font-weight: 800; color: var(--navy-900);">TOTAL AKHIR FAKTUR:</td>
                            <td style="text-align: right; font-weight: 800; color: var(--sky-600);">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Riwayat Pembayaran Masuk -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Histori Pembayaran Faktur Ini ({{ $sale->payments->count() }})
                </div>
                @if($sale->remaining_balance > 0 && $sale->status !== 'batal')
                    <button type="button" class="btn btn-sm btn-success" onclick="openPaymentModal()">
                        + Catat Pembayaran / Cicilan
                    </button>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Kwitansi</th>
                            <th>Tanggal Bayar</th>
                            <th>Metode</th>
                            <th>No. Ref / Bukti</th>
                            <th style="text-align: right;">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sale->payments as $pay)
                            <tr>
                                <td><strong>{{ $pay->payment_number }}</strong></td>
                                <td>{{ $pay->payment_date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst($pay->payment_method) }}</span>
                                </td>
                                <td>{{ $pay->reference_number ?: '-' }}</td>
                                <td style="text-align: right; font-weight: 700; color: var(--success);">
                                    Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-light); padding: 25px;">
                                    Belum ada catatan pembayaran. Faktur berstatus <strong>Belum Bayar</strong>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Info & Actions -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Informasi Faktur & SPPG</div>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 14px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Pelanggan SPPG</div>
                    <div style="font-weight: 700; color: var(--navy-900); font-size: 15px;">
                        <a href="{{ route('sppgs.show', $sale->sppg) }}" style="color: var(--sky-600);">
                            {{ $sale->sppg->name }}
                        </a>
                    </div>
                    <div style="font-size: 12px; color: var(--text-sub);">PIC: {{ $sale->sppg->pic_name ?: '-' }}</div>
                    <div style="font-size: 12px; color: var(--text-sub);">Alamat: {{ $sale->sppg->address ?: '-' }}</div>
                </div>

                <div style="margin-bottom: 14px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Tanggal Penjualan</div>
                    <div style="font-weight: 600;">{{ $sale->sale_date->translatedFormat('l, d F Y') }}</div>
                </div>

                <div style="margin-bottom: 14px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Jatuh Tempo Pembayaran</div>
                    <div style="font-weight: 600; {{ $sale->due_date && now()->gt($sale->due_date) && $sale->remaining_balance > 0 ? 'color: var(--danger);' : '' }}">
                        {{ $sale->due_date ? $sale->due_date->translatedFormat('l, d F Y') : 'Tanpa batas tempo' }}
                    </div>
                </div>

                @if($sale->order)
                    <div style="margin-bottom: 14px;">
                        <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Tautan Pesanan Awal</div>
                        <div>
                            <a href="{{ route('orders.show', $sale->order) }}" style="font-weight: 600; color: var(--navy-800);">
                                {{ $sale->order->order_number }} &rarr;
                            </a>
                        </div>
                    </div>
                @endif

                <hr style="border: none; border-top: 1px solid var(--border-light); margin: 16px 0;">

                @if($sale->remaining_balance > 0 && $sale->status !== 'batal')
                    <button type="button" class="btn btn-success btn-lg" onclick="openPaymentModal()" style="width: 100%; margin-bottom: 10px;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Catat Pembayaran Masuk
                    </button>
                @endif

                <a href="{{ route('sales.print-invoice', $sale) }}" target="_blank" class="btn btn-navy" style="width: 100%; margin-bottom: 8px;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Cetak Faktur Standar A4
                </a>

                <a href="{{ route('sales.print-thermal', $sale) }}" target="_blank" class="btn btn-white" style="width: 100%; margin-bottom: 14px;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/></svg>
                    Cetak Nota Struk Thermal
                </a>

                @if($sale->status !== 'batal')
                    <form action="{{ route('sales.cancel', $sale) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi penjualan ini? Stok produk akan otomatis DIKEMBALIKAN ke gudang.');">
                        @csrf
                        <input type="hidden" name="reason" value="Pembatalan oleh admin">
                        <button type="submit" class="btn btn-sm btn-white" style="width: 100%; color: var(--danger);">
                            Batalkan Penjualan & Kembalikan Stok
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Catat Pembayaran -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-title">Catat Pembayaran SPPG</div>
            <button type="button" class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            <input type="hidden" name="sale_id" value="{{ $sale->id }}">
            <div class="modal-body">
                <div style="background: var(--sky-50); border: 1px solid var(--sky-200); border-radius: var(--radius-md); padding: 14px; margin-bottom: 16px;">
                    <div style="font-size: 13px; color: var(--text-sub);">Faktur: <strong>{{ $sale->invoice_number }}</strong> • {{ $sale->sppg->name }}</div>
                    <div style="font-size: 14px; margin-top: 4px;">Sisa Piutang Saat Ini: <strong style="color: var(--danger); font-size: 17px;">Rp {{ number_format($sale->remaining_balance, 0, ',', '.') }}</strong></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah Pembayaran yang Diterima (Rp) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="1" max="{{ $sale->remaining_balance }}" name="amount" class="form-input" value="{{ $sale->remaining_balance }}" required style="font-size: 17px; font-weight: 700; color: var(--success);">
                    <div class="form-hint">Dapat diisi sebagian (cicilan) atau penuh untuk pelunasan.</div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Tanggal Pembayaran <span class="required">*</span></label>
                            <input type="date" name="payment_date" class="form-input" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="payment_method" class="form-select">
                                <option value="transfer">Transfer Bank</option>
                                <option value="tunai">Tunai / Kas Langsung</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Referensi Transfer / Rekening</label>
                    <input type="text" name="reference_number" class="form-input" placeholder="Contoh: REF-BCA-123456 / Bukti transfer WA">
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" class="form-textarea" placeholder="Keterangan tambahan penerimaan pembayaran"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" onclick="closePaymentModal()">Batal</button>
                <button type="submit" class="btn btn-success">
                    Simpan Pembayaran Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openPaymentModal() {
        document.getElementById('paymentModal').classList.add('active');
    }
    function closePaymentModal() {
        document.getElementById('paymentModal').classList.remove('active');
    }
</script>
@endsection
