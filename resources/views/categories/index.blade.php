@extends('layouts.app')

@section('title', 'Kategori Produk — Nutrisaka')
@section('header_title', 'Kategori Bahan Pangan')
@section('header_subtitle', 'Pengelompokan jenis produk nutrisi & bahan makanan SPPG')

@section('header_actions')
    <a href="{{ route('products.index') }}" class="btn btn-white">&larr; Kembali ke Katalog</a>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px; align-items: start;">
    <!-- Form Tambah Kategori -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">+ Tambah Kategori Baru</div>
        </div>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Nama Kategori <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="Contoh: Sayuran Segar" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan / Catatan</label>
                    <textarea name="description" rows="3" class="form-textarea" placeholder="Deskripsi jenis bahan makanan ini"></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Kategori -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">Daftar Kategori Pangan ({{ $categories->count() }})</div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Keterangan</th>
                        <th>Jumlah Produk</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td>
                                <strong>{{ $cat->name }}</strong>
                            </td>
                            <td>{{ $cat->description ?: '-' }}</td>
                            <td>
                                <span class="badge badge-info">{{ $cat->products_count }} Produk</span>
                            </td>
                            <td style="text-align: right;">
                                <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" {{ $cat->products_count > 0 ? 'disabled title="Kategori masih memiliki produk"' : '' }}>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-light); padding: 30px;">
                                Belum ada kategori tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
