@extends('layouts.app')

@section('title', 'Edit ' . $sppg->name . ' — Nutrisaka')
@section('header_title', 'Edit Data SPPG')
@section('header_subtitle', 'Perbarui informasi pelanggan unit SPPG ' . $sppg->name)

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Perbarui Data Pelanggan: {{ $sppg->name }}</div>
            <a href="{{ route('sppgs.show', $sppg) }}" class="btn btn-sm btn-white">&larr; Kembali ke Profil</a>
        </div>
        <form action="{{ route('sppgs.update', $sppg) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Kode SPPG <span class="required">*</span></label>
                            <input type="text" name="code" class="form-input" value="{{ old('code', $sppg->code) }}" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Nama Satuan Pelayanan (SPPG) <span class="required">*</span></label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $sppg->name) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Nama Penanggung Jawab / PIC</label>
                            <input type="text" name="pic_name" class="form-input" value="{{ old('pic_name', $sppg->pic_name) }}">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Nomor Kontak WhatsApp / Telp</label>
                            <input type="text" name="phone" class="form-input" value="{{ old('phone', $sppg->phone) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Lengkap SPPG</label>
                    <textarea name="address" rows="3" class="form-textarea">{{ old('address', $sppg->address) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Tambahan / Plafon Kredit</label>
                    <textarea name="notes" rows="2" class="form-textarea">{{ old('notes', $sppg->notes) }}</textarea>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $sppg->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        <span style="font-weight: 600; font-size: 14px;">Status SPPG Aktif</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer" style="background: var(--bg-alt); padding: 18px 24px;">
                <a href="{{ route('sppgs.show', $sppg) }}" class="btn btn-white">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Perbarui Data SPPG
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
