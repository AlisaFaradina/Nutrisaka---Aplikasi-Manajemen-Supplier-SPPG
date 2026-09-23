@extends('layouts.app')

@section('title', 'Buat Pesanan SPPG — Nutrisaka')
@section('header_title', 'Catat Pesanan Baru SPPG')
@section('header_subtitle', 'Masukkan daftar kebutuhan bahan makanan yang dipesan oleh unit SPPG')

@section('header_actions')
    <a href="{{ route('orders.index') }}" class="btn btn-white">&larr; Kembali ke Daftar</a>
@endsection

@section('content')
<form action="{{ route('orders.store') }}" method="POST" id="orderForm">
    @csrf
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">
        <!-- Left: Order Items & Notes -->
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        Daftar Produk yang Dipesan
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="btnAddItem">
                        + Tambah Baris Barang
                    </button>
                </div>
                <div class="card-body" style="padding: 16px 20px;">
                    <div style="display: grid; grid-template-columns: 3fr 1.2fr 1.5fr 1.5fr 40px; gap: 10px; font-weight: 700; font-size: 12px; color: var(--text-sub); text-transform: uppercase; margin-bottom: 8px; padding: 0 10px;">
                        <div>Pilih Produk Pangan</div>
                        <div>Jumlah</div>
                        <div>Harga Satuan (Rp)</div>
                        <div>Subtotal (Rp)</div>
                        <div></div>
                    </div>

                    <div id="itemsContainer">
                        <!-- Dynamic item rows will be appended here -->
                    </div>

                    <div style="margin-top: 14px;">
                        <button type="button" class="btn btn-outline" id="btnAddItemSecondary" style="width: 100%;">
                            + Tambah Produk Lainnya
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Catatan Tambahan Pesanan</div>
                </div>
                <div class="card-body">
                    <textarea name="notes" rows="3" class="form-textarea" placeholder="Contoh: Pengiriman pagi maksimal pukul 06.00 WIB untuk masak sarapan. Sayur harus dipotong segar.">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Right: SPPG details & Submit -->
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Informasi Pesanan</div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Pelanggan SPPG <span class="required">*</span></label>
                        <select name="sppg_id" class="form-select" required>
                            <option value="">-- Pilih Satuan SPPG --</option>
                            @foreach($sppgs as $sppg)
                                <option value="{{ $sppg->id }}" {{ (old('sppg_id', request('sppg_id')) == $sppg->id) ? 'selected' : '' }}>
                                    {{ $sppg->name }} ({{ $sppg->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Pesanan Masuk <span class="required">*</span></label>
                        <input type="date" name="order_date" class="form-input" value="{{ old('order_date', now()->toDateString()) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Pengiriman yang Diminta</label>
                        <input type="date" name="delivery_date" class="form-input" value="{{ old('delivery_date', now()->addDay()->toDateString()) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status Awal Pesanan <span class="required">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="menunggu" {{ old('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Diproses</option>
                            <option value="diproses" {{ old('status') === 'diproses' ? 'selected' : '' }}>Langsung Diproses</option>
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Simpan Sebagai Draf</option>
                        </select>
                        <div class="form-hint" style="color: var(--sky-600);">
                            *Pesanan draf/menunggu <strong>tidak memotong stok</strong> sampai diselesaikan.
                        </div>
                    </div>

                    <hr style="border: none; border-top: 1px solid var(--border-light); margin: 20px 0;">

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <span style="font-size: 15px; font-weight: 700; color: var(--text-sub);">Estimasi Total:</span>
                        <span id="grandTotalDisplay" style="font-size: 22px; font-weight: 800; color: var(--navy-900);">Rp 0</span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Simpan Pesanan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    const productsData = @json($products);
    const container = document.getElementById('itemsContainer');
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    let rowIndex = 0;

    function createRow(selectedProductId = '', qty = 1) {
        const row = document.createElement('div');
        row.className = 'item-row';
        row.dataset.index = rowIndex;

        let optionsHtml = '<option value="">-- Pilih Produk --</option>';
        productsData.forEach(p => {
            const isSelected = String(p.id) === String(selectedProductId) ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-price="${p.selling_price}" data-unit="${p.unit}" data-stock="${p.current_stock}" ${isSelected}>
                ${p.name} (Stok: ${p.current_stock} ${p.unit})
            </option>`;
        });

        row.innerHTML = `
            <div>
                <select name="items[${rowIndex}][product_id]" class="form-select product-select" required>
                    ${optionsHtml}
                </select>
                <div class="product-info-hint" style="font-size: 11px; color: var(--text-sub); margin-top: 3px;"></div>
            </div>
            <div>
                <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" class="form-input qty-input" value="${qty}" placeholder="Qty" required>
            </div>
            <div>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" class="form-input price-input" value="0" placeholder="Harga" required>
            </div>
            <div>
                <input type="text" class="form-input subtotal-input" value="Rp 0" readonly style="background: var(--bg-alt); font-weight: 700; text-align: right;">
            </div>
            <div>
                <button type="button" class="btn-remove-row" title="Hapus baris">&times;</button>
            </div>
        `;

        container.appendChild(row);

        const select = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.qty-input');
        const priceInput = row.querySelector('.price-input');
        const subtotalInput = row.querySelector('.subtotal-input');
        const btnRemove = row.querySelector('.btn-remove-row');
        const hint = row.querySelector('.product-info-hint');

        function updateRowSubtotal() {
            const q = parseFloat(qtyInput.value) || 0;
            const p = parseFloat(priceInput.value) || 0;
            const sub = q * p;
            subtotalInput.value = 'Rp ' + sub.toLocaleString('id-ID');
            calculateGrandTotal();
        }

        select.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                const price = parseFloat(opt.dataset.price) || 0;
                const unit = opt.dataset.unit || '';
                const stock = opt.dataset.stock || 0;
                priceInput.value = price;
                hint.textContent = `Satuan: ${unit} | Stok Gudang: ${stock} ${unit}`;
            } else {
                priceInput.value = 0;
                hint.textContent = '';
            }
            updateRowSubtotal();
        });

        qtyInput.addEventListener('input', updateRowSubtotal);
        priceInput.addEventListener('input', updateRowSubtotal);

        btnRemove.addEventListener('click', function() {
            if (container.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calculateGrandTotal();
            } else {
                alert('Pesanan harus memiliki minimal satu produk.');
            }
        });

        if (selectedProductId) {
            select.dispatchEvent(new Event('change'));
        }

        rowIndex++;
    }

    function calculateGrandTotal() {
        let grand = 0;
        document.querySelectorAll('.item-row').forEach(r => {
            const q = parseFloat(r.querySelector('.qty-input').value) || 0;
            const p = parseFloat(r.querySelector('.price-input').value) || 0;
            grand += (q * p);
        });
        grandTotalDisplay.textContent = 'Rp ' + grand.toLocaleString('id-ID');
    }

    document.getElementById('btnAddItem').addEventListener('click', () => createRow());
    document.getElementById('btnAddItemSecondary').addEventListener('click', () => createRow());

    // Inisialisasi baris pertama saat form dibuka
    createRow();
</script>
@endsection
