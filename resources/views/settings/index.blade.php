@extends('layouts.app')

@section('title', 'Pengaturan & Backup Data — Nutrisaka')
@section('header_title', 'Pengaturan Aplikasi & Keamanan')
@section('header_subtitle', 'Kelola identitas usaha supplier, rekening faktur, keamanan PIN, dan pencadangan data')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start;">
    <!-- Left: Profil Supplier & Rekening -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profil Usaha Supplier Pangan
                </div>
            </div>
            <form action="{{ route('settings.update-profile') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Nama Perusahaan / Supplier <span class="required">*</span></label>
                                <input type="text" name="supplier_name" class="form-input" value="{{ old('supplier_name', $settings['supplier_name']) }}" required>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Slogan / Tagline Usaha</label>
                                <input type="text" name="supplier_tagline" class="form-input" value="{{ old('supplier_tagline', $settings['supplier_tagline']) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Nomor Telepon / WhatsApp</label>
                                <input type="text" name="supplier_phone" class="form-input" value="{{ old('supplier_phone', $settings['supplier_phone']) }}">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Alamat Email</label>
                                <input type="email" name="supplier_email" class="form-input" value="{{ old('supplier_email', $settings['supplier_email']) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat Kantor / Gudang Logistik</label>
                        <textarea name="supplier_address" rows="2" class="form-textarea">{{ old('supplier_address', $settings['supplier_address']) }}</textarea>
                    </div>

                    <hr style="border: none; border-top: 1px solid var(--border-light); margin: 20px 0;">
                    <div style="font-weight: 700; color: var(--navy-900); margin-bottom: 14px; font-size: 15px;">
                        Rekening Bank Penerimaan (Tampil di Faktur Invoice):
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Nama Bank</label>
                                <input type="text" name="bank_name" class="form-input" placeholder="Contoh: Bank Mandiri / BCA / BRI" value="{{ old('bank_name', $settings['bank_name']) }}">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" name="bank_account_number" class="form-input" placeholder="Contoh: 137-00-1234567-8" value="{{ old('bank_account_number', $settings['bank_account_number']) }}">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Atas Nama Rekening</label>
                                <input type="text" name="bank_account_name" class="form-input" placeholder="Contoh: CV NUTRISAKA PANGAN" value="{{ old('bank_account_name', $settings['bank_account_name']) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Catatan Kaki Faktur / Invoice</label>
                                <textarea name="invoice_footer_notes" rows="2" class="form-textarea">{{ old('invoice_footer_notes', $settings['invoice_footer_notes']) }}</textarea>
                            </div>
                        </div>
                        <div class="form-col" style="max-width: 220px;">
                            <div class="form-group">
                                <label class="form-label">Ukuran Kertas Struk Kasir</label>
                                <select name="thermal_paper_size" class="form-select">
                                    <option value="58mm" {{ $settings['thermal_paper_size'] === '58mm' ? 'selected' : '' }}>58mm (Kecil/Standar)</option>
                                    <option value="80mm" {{ $settings['thermal_paper_size'] === '80mm' ? 'selected' : '' }}>80mm (Lebar)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div style="text-align: right; margin-top: 10px;">
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Right: Backup, Restore & PIN -->
    <div>
        <!-- Lisensi Perangkat Nutrisaka -->
        <div class="card" style="margin-bottom: 24px; border-left: 4px solid var(--sky-500);">
            <div class="card-header" style="background: var(--sky-50); border-bottom-color: var(--sky-200); display: flex; justify-content: space-between; align-items: center;">
                <div class="card-title" style="color: var(--navy-900);">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Lisensi Perangkat Ini
                </div>
                @if($license && $license->isActive())
                    <span class="badge badge-success" style="font-size: 11px;">Aktif Selamanya</span>
                @else
                    <span class="badge badge-danger" style="font-size: 11px;">Terkunci</span>
                @endif
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 13px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-light); padding-bottom: 6px;">
                        <span style="color: var(--text-sub);">Token Perangkat:</span>
                        <strong style="font-family: monospace; color: var(--navy-900);">{{ $license ? $license->token_masked : '-' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-light); padding-bottom: 6px;">
                        <span style="color: var(--text-sub);">Hardware ID:</span>
                        <strong style="font-family: monospace; color: var(--navy-900);">{{ $shortFingerprint }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-light); padding-bottom: 6px;">
                        <span style="color: var(--text-sub);">Masa Berlaku:</span>
                        <strong style="color: var(--success);">Aktif Selamanya (Lifetime)</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-light); padding-bottom: 6px;">
                        <span style="color: var(--text-sub);">Ketentuan:</span>
                        <span>1 Token = 1 Perangkat Fisik</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-sub);">Email Admin:</span>
                        <strong style="color: var(--navy-800);">sakanutri@gmail.com</strong>
                    </div>
                </div>

                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <!-- Buka Panel Admin -->
                    <a href="{{ route('admin.dashboard') }}" target="_blank" class="btn btn-outline" style="flex: 1; font-size: 12px; padding: 7px 10px; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <span>Panel Admin</span>
                    </a>

                    <!-- Nonaktifkan / Logout Perangkat -->
                    <form action="{{ route('settings.license.deactivate') }}" method="POST" style="margin: 0;" onsubmit="return confirm('PERINGATAN: Melepas lisensi dari perangkat ini akan membuat aplikasi kembali ke halaman login token. Token dapat Anda gunakan kembali jika diinginkan. Lanjutkan?');">
                        @csrf
                        <button type="submit" class="btn btn-danger" style="font-size: 12px; padding: 7px 10px;" title="Keluar / Lepas lisensi dari perangkat ini">
                            Lepas Lisensi (Keluar)
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Backup & Restore Data Lokal -->
        <div class="card">
            <div class="card-header" style="background: var(--sky-50); border-bottom-color: var(--sky-200);">
                <div class="card-title" style="color: var(--sky-600);">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Cadangan & Pemulihan (Offline)
                </div>
            </div>
            <div class="card-body">
                <p style="font-size: 13px; color: var(--text-sub); margin-bottom: 14px;">
                    Karena aplikasi bekerja offline secara lokal, simpan cadangan (*backup*) secara berkala ke flashdisk atau folder aman.
                </p>

                <!-- Download Backup -->
                <a href="{{ route('settings.export-backup') }}" class="btn btn-primary" style="width: 100%; margin-bottom: 16px;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Unduh Backup Data (JSON)
                </a>

                <hr style="border: none; border-top: 1px dashed var(--border-light); margin: 16px 0;">

                <!-- Restore Backup -->
                <form action="{{ route('settings.import-restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('PERINGATAN: Memulihkan cadangan akan menggantikan data saat ini dengan data dari file backup. Lanjutkan?');">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" style="font-size: 13px;">Pulihkan Data dari File Cadangan:</label>
                        <input type="file" name="backup_file" accept=".json,.txt" class="form-input" required style="padding: 6px;">
                    </div>
                    <button type="submit" class="btn btn-navy" style="width: 100%;">
                        Pulihkan Data (Restore)
                    </button>
                </form>
            </div>
        </div>

        <!-- Keamanan PIN -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    PIN Keamanan Aplikasi
                </div>
            </div>
            <form action="{{ route('settings.update-pin') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">PIN Lama</label>
                        <input type="password" name="current_pin" class="form-input" placeholder="Masukkan PIN saat ini">
                    </div>
                    <div class="form-group">
                        <label class="form-label">PIN Baru (4-6 Angka) <span class="required">*</span></label>
                        <input type="password" name="new_pin" class="form-input" placeholder="Contoh: 1234" required>
                    </div>
                    <button type="submit" class="btn btn-white" style="width: 100%;">
                        Perbarui PIN Keamanan
                    </button>
                </div>
            </form>
        </div>

        <!-- Simulasi Data Contoh -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Data Simulasi & Contoh</div>
            </div>
            <div class="card-body">
                <p style="font-size: 13px; color: var(--text-sub); margin-bottom: 14px;">
                    Muat data contoh master SPPG, produk bahan pangan segar, pesanan masuk, dan transaksi penjualan untuk keperluan demo.
                </p>
                <form action="{{ route('settings.load-demo-data') }}" method="POST" onsubmit="return confirm('Muat data contoh simulasi Nutrisaka?');">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="width: 100%;">
                        Muat Data Contoh Simulasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
