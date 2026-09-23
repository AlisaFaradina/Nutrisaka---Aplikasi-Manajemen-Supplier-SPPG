@extends('layouts.app')

@section('title', 'Edit ' . $product->name . ' — Nutrisaka')
@section('header_title', 'Edit Data Produk')
@section('header_subtitle', 'Perbarui rincian produk, harga jual, dan batas stok')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Perbarui Produk: {{ $product->name }}</div>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-white">&larr; Kembali ke Katalog</a>
        </div>
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Kode SKU / Kode Barang <span class="required">*</span></label>
                            <input type="text" name="sku" class="form-input" value="{{ old('sku', $product->sku) }}" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Kategori Pangan <span class="required">*</span></label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Bahan Pangan / Produk <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Satuan Ukuran / Kemasan <span class="required">*</span></label>
                            <input type="text" name="unit" list="unit-list" class="form-input" value="{{ old('unit', $product->unit) }}" required>
                            <datalist id="unit-list">
                                @foreach($commonUnits as $unit)
                                    <option value="{{ $unit }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Harga Jual ke SPPG (Rp) <span class="required">*</span></label>
                            <input type="number" step="0.01" name="selling_price" class="form-input" value="{{ old('selling_price', $product->selling_price) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Harga Beli / Pokok (Rp)</label>
                            <input type="number" step="0.01" name="cost_price" class="form-input" value="{{ old('cost_price', $product->cost_price) }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Stok Saat Ini (Hanya Baca)</label>
                            <input type="text" class="form-input" value="{{ $product->current_stock }} {{ $product->unit }}" disabled style="background: var(--bg-alt); font-weight: bold;">
                            <div class="form-hint">Untuk ubah stok, gunakan menu Barang Masuk atau Penyesuaian Stok</div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Batas Stok Minimum <span class="required">*</span></label>
                            <input type="number" step="0.01" name="min_stock" class="form-input" value="{{ old('min_stock', $product->min_stock) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan Produk</label>
                    <textarea name="description" rows="2" class="form-textarea">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 14px;">Produk Aktif</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="background: var(--bg-alt); padding: 18px 24px;">
                <a href="{{ route('products.index') }}" class="btn btn-white">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Perbarui Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
