@extends('layouts.app')

@section('title', 'Pesanan ' . $order->order_number . ' — Nutrisaka')
@section('header_title', 'Rincian Pesanan SPPG')
@section('header_subtitle', $order->order_number . ' • ' . $order->sppg->name)

@section('header_actions')
    <a href="{{ route('orders.index') }}" class="btn btn-white">&larr; Kembali ke Daftar</a>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start;">
    <!-- Left Column: Items Table & Notes -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                    Daftar Barang Dipesan ({{ $order->items->count() }} Item)
                </div>
                <div>
                    @if($order->status === 'selesai')
                        <span class="badge badge-success">Status: Selesai (Terjual)</span>
                    @elseif($order->status === 'diproses')
                        <span class="badge badge-info">Status: Sedang Diproses</span>
                    @elseif($order->status === 'menunggu')
                        <span class="badge badge-warning">Status: Menunggu Diproses</span>
                    @elseif($order->status === 'dibatalkan')
                        <span class="badge badge-danger">Status: Dibatalkan</span>
                    @else
                        <span class="badge badge-secondary">Status: Draf</span>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk Bahan Makanan</th>
                            <th>Kategori</th>
                            <th style="text-align: right;">Kuantitas</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong style="color: var(--navy-900);">{{ $item->product->name }}</strong>
                                    <div style="font-size: 11px; color: var(--text-sub);">SKU: {{ $item->product->sku }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $item->product->category->name }}</span>
                                </td>
                                <td style="text-align: right; font-weight: 700;">
                                    {{ $item->quantity }} {{ $item->product->unit }}
                                </td>
                                <td style="text-align: right;">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; font-weight: 700; color: var(--sky-600);">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: var(--bg-alt); font-size: 16px;">
                            <th colspan="5" style="text-align: right; font-weight: 700;">Total Perkiraan Pesanan:</th>
                            <th style="text-align: right; font-weight: 800; color: var(--navy-900);">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($order->notes)
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Catatan Khusus Pesanan</div>
                </div>
                <div class="card-body">
                    <p style="color: var(--text-muted); font-size: 14px; white-space: pre-line;">{{ $order->notes }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Status & Conversion Actions -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Informasi Pemesanan</div>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 14px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Pelanggan SPPG</div>
                    <div style="font-weight: 700; color: var(--navy-900); font-size: 15px;">
                        <a href="{{ route('sppgs.show', $order->sppg) }}" style="color: var(--sky-600);">
                            {{ $order->sppg->name }}
                        </a>
                    </div>
                    <div style="font-size: 12px; color: var(--text-sub);">PIC: {{ $order->sppg->pic_name ?: '-' }} ({{ $order->sppg->phone ?: '-' }})</div>
                </div>

                <div style="margin-bottom: 14px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Tanggal Pesanan Masuk</div>
                    <div style="font-weight: 600;">{{ $order->order_date->translatedFormat('l, d F Y') }}</div>
                </div>

                <div style="margin-bottom: 14px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: var(--text-sub); font-weight: 700;">Target Tanggal Pengiriman</div>
                    <div style="font-weight: 600;">
                        {{ $order->delivery_date ? $order->delivery_date->translatedFormat('l, d F Y') : 'Tidak ditentukan' }}
                    </div>
                </div>

                <hr style="border: none; border-top: 1px solid var(--border-light); margin: 16px 0;">

                <!-- Workflow Actions -->
                <div style="font-size: 13px; font-weight: 700; color: var(--navy-900); margin-bottom: 12px;">
                    Alur Proses Pesanan:
                </div>

                @if($order->sale)
                    <div class="alert alert-success" style="margin-bottom: 0;">
                        <div>
                            <strong>Sudah Jadi Transaksi Penjualan</strong>
                            <div style="font-size: 13px; margin-top: 4px;">Faktur: <strong>{{ $order->sale->invoice_number }}</strong></div>
                            <div style="margin-top: 10px;">
                                <a href="{{ route('sales.show', $order->sale) }}" class="btn btn-sm btn-navy">
                                    Buka Faktur Penjualan &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    @if($order->status !== 'selesai' && $order->status !== 'dibatalkan')
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @if($order->status === 'menunggu')
                                <form action="{{ route('orders.update-status', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="diproses">
                                    <button type="submit" class="btn btn-navy" style="width: 100%;">
                                        Setujui & Mulai Proses Pesanan
                                    </button>
                                </form>
                            @endif

                            <!-- Tombol Modal Proses ke Penjualan -->
                            <button type="button" class="btn btn-primary btn-lg" onclick="openConvertModal()" style="width: 100%;">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                Selesaikan & Buat Penjualan
                            </button>

                            <form action="{{ route('orders.update-status', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="dibatalkan">
                                <button type="submit" class="btn btn-sm btn-white" style="width: 100%; color: var(--danger);">
                                    Batalkan Pesanan Ini
                                </button>
                            </form>
                        </div>
                    @elseif($order->status === 'dibatalkan')
                        <div class="alert alert-error" style="margin-bottom: 0;">
                            Pesanan ini telah dibatalkan dan tidak diproses ke penjualan.
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Konversi ke Penjualan -->
<div class="modal-overlay" id="convertModal">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-title">Proses Pesanan ke Penjualan & Faktur</div>
            <button type="button" class="modal-close" onclick="closeConvertModal()">&times;</button>
        </div>
        <form action="{{ route('orders.convert-to-sale', $order) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="alert alert-info">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <span>Setelah diselesaikan, stok produk akan <strong>otomatis berkurang</strong> dan faktur invoice akan diterbitkan.</span>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Tanggal Penjualan / Pengiriman <span class="required">*</span></label>
                            <input type="date" name="sale_date" class="form-input" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Tanggal Jatuh Tempo Pembayaran</label>
                            <input type="date" name="due_date" class="form-input" value="{{ now()->addDays(7)->toDateString() }}">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Potongan Harga / Diskon (Rp)</label>
                            <input type="number" step="0.01" min="0" name="discount" class="form-input" value="0">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Pembayaran Awal / DP (Rp)</label>
                            <input type="number" step="0.01" min="0" name="initial_paid" class="form-input" value="0" placeholder="0 jika belum bayar">
                        </div>
                    </div>
                </div>

                <div class="form-row">
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
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Nomor Referensi / No Rekening (Opsional)</label>
                            <input type="text" name="payment_reference" class="form-input" placeholder="No. ref transfer / bukti">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" onclick="closeConvertModal()">Batal</button>
                <button type="submit" class="btn btn-primary">
                    Terbitkan Faktur Penjualan &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openConvertModal() {
        document.getElementById('convertModal').classList.add('active');
    }
    function closeConvertModal() {
        document.getElementById('convertModal').classList.remove('active');
    }
</script>
@endsection
