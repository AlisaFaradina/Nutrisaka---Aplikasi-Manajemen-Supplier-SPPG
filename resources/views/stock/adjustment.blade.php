@extends('layouts.app')

@section('title', 'Penyesuaian Stok (Opname) — Nutrisaka')
@section('header_title', 'Penyesuaian Stok (Stock Opname)')
@section('header_subtitle', 'Sesuaikan saldo sistem dengan jumlah fisik riil di gudang / pencatatan susut')

@section('header_actions')
    <a href="{{ route('stock.index') }}" class="btn btn-white">&larr; Kembali ke Gudang</a>
@endsection

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div class="card">
        <div class="card-header" style="background: var(--warning-bg); border-bottom-color: var(--warning-border);">
            <div class="card-title" style="color: var(--warning);">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Formulir Stock Opname Fisik
            </div>
        </div>
        <form action="{{ route('stock.store-adjustment') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Pilih Produk Pangan <span class="required">*</span></label>
                    <select name="product_id" id="productSelect" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-unit="{{ $p->unit }}" data-current="{{ $p->current_stock }}" {{ (old('product_id', request('product_id')) == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }} (SKU: {{ $p->sku }} • Stok Sistem: {{ $p->current_stock }} {{ $p->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Stok Tercatat di Sistem</label>
                            <input type="text" id="currentStockDisplay" class="form-input" value="0" readonly style="background: var(--bg-alt); font-weight: bold;">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Jumlah Fisik Riil Hasil Hitung <span class="required">*</span></label>
                            <input type="number" step="0.01" min="0" name="physical_qty" id="physicalQtyInput" class="form-input" placeholder="0" value="{{ old('physical_qty') }}" required style="font-size: 16px; font-weight: 700; color: var(--navy-900);">
                            <div class="form-hint" id="diffHint">Selisih: 0</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alasan / Keterangan Penyesuaian <span class="required">*</span></label>
                    <textarea name="notes" rows="3" class="form-textarea" placeholder="Contoh: Susut bobot sayur selama penyimpanan / telur pecah di gudang / koreksi hitung stok fisik mingguan" required>{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="modal-footer" style="background: var(--bg-alt); padding: 18px 24px;">
                <a href="{{ route('stock.index') }}" class="btn btn-white">Batal</a>
                <button type="submit" class="btn btn-warning">
                    Simpan Penyesuaian Opname
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const pSelect = document.getElementById('productSelect');
    const currentStockDisplay = document.getElementById('currentStockDisplay');
    const physicalQtyInput = document.getElementById('physicalQtyInput');
    const diffHint = document.getElementById('diffHint');

    function updateStockInfo() {
        const opt = pSelect.options[pSelect.selectedIndex];
        if (opt && opt.value) {
            const cur = parseFloat(opt.dataset.current) || 0;
            const unit = opt.dataset.unit || '';
            currentStockDisplay.value = `${cur} ${unit}`;
            updateDiff();
        } else {
            currentStockDisplay.value = '0';
            diffHint.textContent = 'Selisih: 0';
        }
    }

    function updateDiff() {
        const opt = pSelect.options[pSelect.selectedIndex];
        if (opt && opt.value) {
            const cur = parseFloat(opt.dataset.current) || 0;
            const physical = parseFloat(physicalQtyInput.value) || 0;
            const diff = physical - cur;
            const unit = opt.dataset.unit || '';
            if (diff > 0) {
                diffHint.innerHTML = `Selisih: <span style="color: var(--success); font-weight: bold;">+${diff} ${unit} (Lebih)</span>`;
            } else if (diff < 0) {
                diffHint.innerHTML = `Selisih: <span style="color: var(--danger); font-weight: bold;">${diff} ${unit} (Kurang/Susut)</span>`;
            } else {
                diffHint.innerHTML = `Selisih: <span>0 ${unit} (Sesuai)</span>`;
            }
        }
    }

    pSelect.addEventListener('change', updateStockInfo);
    physicalQtyInput.addEventListener('input', updateDiff);
    updateStockInfo();
</script>
@endsection
