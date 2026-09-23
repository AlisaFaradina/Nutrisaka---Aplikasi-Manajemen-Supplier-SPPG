@extends('layouts.app')

@section('title', 'Pencatatan Barang Masuk — Nutrisaka')
@section('header_title', 'Penerimaan Barang Masuk (+)')
@section('header_subtitle', 'Catat pasokan barang pangan yang masuk dari petani, distributor, atau pengadaan baru')

@section('header_actions')
    <a href="{{ route('stock.index') }}" class="btn btn-white">&larr; Kembali ke Gudang</a>
@endsection

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div class="card">
        <div class="card-header" style="background: var(--success-bg); border-bottom-color: var(--success-border);">
            <div class="card-title" style="color: var(--success);">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 5v14M5 12h14"/></svg>
                Formulir Penerimaan Barang Masuk
            </div>
        </div>
        <form action="{{ route('stock.store-in') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Pilih Produk Pangan <span class="required">*</span></label>
                    <select name="product_id" id="productSelect" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-unit="{{ $p->unit }}" data-current="{{ $p->current_stock }}" {{ (old('product_id', request('product_id')) == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }} (SKU: {{ $p->sku }} • Stok Sekarang: {{ $p->current_stock }} {{ $p->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Kuantitas / Jumlah Masuk <span class="required">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="quantity" id="qtyInput" class="form-input" placeholder="Contoh: 50" value="{{ old('quantity') }}" required style="font-size: 16px; font-weight: 700; color: var(--success);">
                            <div class="form-hint" id="unitHint">Satuan: -</div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Sumber Pasokan / Pengadaan <span class="required">*</span></label>
                            <select name="source" class="form-select" required>
                                <option value="purchase">Pembelian dari Petani / Pasar Induk</option>
                                <option value="supplier_upstream">Distributor Pabrik Pangan</option>
                                <option value="harvest">Hasil Panen Mitra Supplier</option>
                                <option value="initial">Penerimaan Lainnya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Pengadaan / Nomor Surat Jalan Pemasok</label>
                    <textarea name="notes" rows="3" class="form-textarea" placeholder="Contoh: Pengiriman DO dari Petani Cianjur No. SJ-88712. Kualitas bagus grade A.">{{ old('notes') }}</textarea>
                </div>

                <div style="background: var(--bg-alt); padding: 14px; border-radius: var(--radius-md); font-size: 13px; color: var(--text-sub);">
                    Setelah disimpan, kuantitas produk akan <strong>bertambah secara instan</strong> dan tercatat ke histori kartu stok.
                </div>
            </div>
            <div class="modal-footer" style="background: var(--bg-alt); padding: 18px 24px;">
                <a href="{{ route('stock.index') }}" class="btn btn-white">Batal</a>
                <button type="submit" class="btn btn-success">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Simpan Stok Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const pSelect = document.getElementById('productSelect');
    const unitHint = document.getElementById('unitHint');

    function updateHint() {
        const opt = pSelect.options[pSelect.selectedIndex];
        if (opt && opt.value) {
            unitHint.textContent = `Satuan: ${opt.dataset.unit} (Stok Saat Ini: ${opt.dataset.current} ${opt.dataset.unit})`;
        } else {
            unitHint.textContent = 'Satuan: -';
        }
    }

    pSelect.addEventListener('change', updateHint);
    updateHint();
</script>
@endsection
