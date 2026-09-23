@extends('layouts.app')

@section('title', 'Tambah SPPG Baru — Nutrisaka')
@section('header_title', 'Tambah Pelanggan SPPG')
@section('header_subtitle', 'Daftarkan unit Satuan Pelayanan Pemenuhan Gizi atau Dapur Nutrisi baru')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Formulir Pendaftaran SPPG</div>
            <a href="{{ route('sppgs.index') }}" class="btn btn-sm btn-white">&larr; Kembali ke Daftar</a>
        </div>
        <form action="{{ route('sppgs.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Kode SPPG <span class="required">*</span></label>
                            <input type="text" name="code" class="form-input" value="{{ old('code', $generatedCode) }}" required>
                            <div class="form-hint">Kode unik untuk identifikasi unit SPPG</div>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Nama Satuan Pelayanan (SPPG) <span class="required">*</span></label>
                            <input type="text" name="name" class="form-input" placeholder="Contoh: SPPG Dapur Sehat Harapan Mandiri" value="{{ old('name') }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Nama Penanggung Jawab / PIC</label>
                            <input type="text" name="pic_name" class="form-input" placeholder="Contoh: Ibu Hj. Siti Aminah" value="{{ old('pic_name') }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Nomor Kontak WhatsApp / Telp</label>
                            <input type="text" name="phone" class="form-input" placeholder="Contoh: 0812-3456-7890" value="{{ old('phone') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Lengkap SPPG</label>
                    <textarea name="address" rows="3" class="form-textarea" placeholder="Alamat unit dapur/lokasi pengiriman bahan makanan">{{ old('address') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Tambahan / Plafon / Jadwal Pengiriman Khusus</label>
                    <textarea name="notes" rows="2" class="form-textarea" placeholder="Catatan jam penerimaan bahan, batas plafon, atau preferensi supplier">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 14px;">Status SPPG Aktif (Dapat menerima pesanan & faktur baru)</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="background: var(--bg-alt); padding: 18px 24px;">
                <a href="{{ route('sppgs.index') }}" class="btn btn-white">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Data SPPG
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
