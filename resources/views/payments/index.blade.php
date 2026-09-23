@extends('layouts.app')

@section('title', 'Pembayaran & Piutang SPPG — Nutrisaka')
@section('header_title', 'Pembayaran & Piutang SPPG')
@section('header_subtitle', 'Pantau tagihan jatuh tempo, terima pembayaran bertahap, dan riwayat kwitansi')

@section('content')
    <!-- KPI Header -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-danger">
            <div class="kpi-icon danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Total Piutang Berjalan</div>
                <div class="kpi-value" style="color: var(--danger);">Rp {{ number_format($totalOutstandingDebt, 0, ',', '.') }}</div>
                <div class="kpi-subtext">Dari {{ $unpaidInvoicesCount }} faktur belum lunas</div>
            </div>
        </div>

        <div class="kpi-card kpi-success">
            <div class="kpi-icon success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Pembayaran Diterima Bulan Ini</div>
                <div class="kpi-value">Rp {{ number_format($paymentsReceivedThisMonth, 0, ',', '.') }}</div>
                <div class="kpi-subtext">Kas & transfer masuk bulan berjalan</div>
            </div>
        </div>

        <div class="kpi-card kpi-sky">
            <div class="kpi-icon sky">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="kpi-info">
                <div class="kpi-title">Pelanggan SPPG Dilayani</div>
                <div class="kpi-value">{{ $sppgs->count() }} <span style="font-size: 14px; font-weight: normal; color: var(--text-sub);">Unit</span></div>
                <div class="kpi-subtext">Semua unit aktif</div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tabs-nav">
        <a href="{{ route('payments.index', ['tab' => 'piutang']) }}" class="tab-link {{ $tab === 'piutang' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Tagihan Piutang Aktif ({{ $debtSales->total() }})
        </a>
        <a href="{{ route('payments.index', ['tab' => 'riwayat']) }}" class="tab-link {{ $tab === 'riwayat' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Riwayat Kwitansi Pembayaran ({{ $payments->total() }})
        </a>
    </div>

    <!-- Filter Toolbar -->
    <div class="filter-toolbar">
        <form action="{{ route('payments.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; flex-wrap: wrap;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="form-input" placeholder="Cari invoice atau nama SPPG..." value="{{ request('search') }}">
            </div>
            <select name="sppg_id" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="">Semua SPPG</option>
                @foreach($sppgs as $sp)
                    <option value="{{ $sp->id }}" {{ request('sppg_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-navy">Filter</button>
            @if(request('search') || request('sppg_id'))
                <a href="{{ route('payments.index', ['tab' => $tab]) }}" class="btn btn-white">Reset</a>
            @endif
        </form>
    </div>

    @if($tab === 'piutang')
        <!-- Tabel Tagihan Piutang -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Daftar Faktur Belum Lunas</div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Faktur</th>
                            <th>Tgl Penjualan</th>
                            <th>Jatuh Tempo</th>
                            <th>Pelanggan SPPG</th>
                            <th>Total Tagihan</th>
                            <th>Sudah Dibayar</th>
                            <th>Sisa Piutang</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debtSales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" style="font-weight: 700; color: var(--sky-600); font-size: 15px;">
                                        {{ $sale->invoice_number }}
                                    </a>
                                </td>
                                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($sale->due_date)
                                        <span style="{{ now()->gt($sale->due_date) ? 'color: var(--danger); font-weight: 700;' : '' }}">
                                            {{ $sale->due_date->format('d/m/Y') }}
                                            @if(now()->gt($sale->due_date))
                                                (Lewat!)
                                            @endif
                                        </span>
                                    @else
                                        <span style="color: var(--text-light);">-</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $sale->sppg->name }}</strong>
                                    <div style="font-size: 11px; color: var(--text-sub);">PIC: {{ $sale->sppg->pic_name ?: '-' }}</div>
                                </td>
                                <td><strong>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong></td>
                                <td><span style="color: var(--success); font-weight: 600;">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span></td>
                                <td>
                                    <strong style="color: var(--danger); font-size: 15px;">
                                        Rp {{ number_format($sale->remaining_balance, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <button type="button" class="btn btn-sm btn-success" 
                                        onclick="openPayModal('{{ $sale->id }}', '{{ $sale->invoice_number }}', '{{ $sale->sppg->name }}', '{{ $sale->remaining_balance }}')">
                                        + Catat Bayar
                                    </button>
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-white">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--success); padding: 40px;">
                                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 6px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <div style="font-size: 16px; font-weight: 700;">Luar Biasa! Tidak ada piutang yang belum tertagih.</div>
                                    <div style="font-size: 13px; color: var(--text-sub); margin-top: 4px;">Seluruh faktur penjualan SPPG telah berstatus Lunas.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding: 16px 20px;">
                {{ $debtSales->links() }}
            </div>
        </div>
    @else
        <!-- Tabel Riwayat Pembayaran Masuk -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Daftar Kwitansi Penerimaan Pembayaran</div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Kwitansi</th>
                            <th>Tanggal Bayar</th>
                            <th>No. Faktur</th>
                            <th>Pelanggan SPPG</th>
                            <th>Metode Bayar</th>
                            <th>No. Referensi / Bank</th>
                            <th style="text-align: right;">Jumlah Diterima (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                            <tr>
                                <td><strong>{{ $p->payment_number }}</strong></td>
                                <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('sales.show', $p->sale) }}" style="font-weight: 700; color: var(--sky-600);">
                                        {{ $p->sale->invoice_number }}
                                    </a>
                                </td>
                                <td><strong>{{ $p->sale->sppg->name }}</strong></td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst($p->payment_method) }}</span>
                                </td>
                                <td>{{ $p->reference_number ?: '-' }}</td>
                                <td style="text-align: right; font-weight: 800; color: var(--success); font-size: 15px;">
                                    Rp {{ number_format($p->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-light); padding: 40px;">
                                    Belum ada catatan kwitansi pembayaran masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding: 16px 20px;">
                {{ $payments->links() }}
            </div>
        </div>
    @endif

    <!-- Universal Payment Modal -->
    <div class="modal-overlay" id="genericPayModal">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Input Pembayaran SPPG</div>
                <button type="button" class="modal-close" onclick="closePayModal()">&times;</button>
            </div>
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="sale_id" id="modalSaleId">
                <div class="modal-body">
                    <div style="background: var(--sky-50); border: 1px solid var(--sky-200); border-radius: var(--radius-md); padding: 14px; margin-bottom: 16px;">
                        <div style="font-size: 13px; color: var(--text-sub);">Faktur: <strong id="modalInvoiceText"></strong> • <span id="modalSppgText"></span></div>
                        <div style="font-size: 14px; margin-top: 4px;">Sisa Piutang: <strong style="color: var(--danger); font-size: 17px;" id="modalDebtText"></strong></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nominal Pembayaran Diterima (Rp) <span class="required">*</span></label>
                        <input type="number" step="0.01" min="1" name="amount" id="modalAmountInput" class="form-input" required style="font-size: 17px; font-weight: 700; color: var(--success);">
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Tanggal Bayar <span class="required">*</span></label>
                                <input type="date" name="payment_date" class="form-input" value="{{ now()->toDateString() }}" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Metode</label>
                                <select name="payment_method" class="form-select">
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="tunai">Tunai / Cash</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor Referensi Bukti Transfer (Opsional)</label>
                        <input type="text" name="reference_number" class="form-input" placeholder="Contoh: REF-BCA-987654">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan Tambahan</label>
                        <textarea name="notes" rows="2" class="form-textarea" placeholder="Catatan penerimaan pembayaran"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" onclick="closePayModal()">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function openPayModal(saleId, invoiceNum, sppgName, debt) {
        document.getElementById('modalSaleId').value = saleId;
        document.getElementById('modalInvoiceText').textContent = invoiceNum;
        document.getElementById('modalSppgText').textContent = sppgName;
        document.getElementById('modalDebtText').textContent = 'Rp ' + Number(debt).toLocaleString('id-ID');
        document.getElementById('modalAmountInput').max = debt;
        document.getElementById('modalAmountInput').value = debt;
        document.getElementById('genericPayModal').classList.add('active');
    }
    function closePayModal() {
        document.getElementById('genericPayModal').classList.remove('active');
    }
</script>
@endsection
