@extends('admin.layout')

@section('title', 'Manajemen Token Lisensi Perangkat')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(15, 41, 66, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-icon.total { background: #e0f2fe; color: #0284c7; }
    .stat-icon.active { background: #dbeafe; color: #2563eb; }
    .stat-icon.unused { background: #dcfce7; color: #16a34a; }
    .stat-icon.revoked { background: #fee2e2; color: #dc2626; }

    .stat-info h3 {
        margin: 0;
        font-size: 26px;
        font-weight: 800;
        color: #0f2942;
        line-height: 1.1;
    }

    .stat-info p {
        margin: 4px 0 0 0;
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }

    .admin-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(15, 41, 66, 0.04);
        margin-bottom: 28px;
        overflow: hidden;
    }

    .admin-card-header {
        padding: 20px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .admin-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 17px;
        font-weight: 700;
        color: #0f2942;
        margin: 0;
    }

    .admin-card-body {
        padding: 24px;
    }

    .token-badge {
        font-family: 'Courier New', Courier, monospace;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 1px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        padding: 4px 10px;
        border-radius: 6px;
        color: #0f2942;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pill.unused { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .status-pill.active { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
    .status-pill.revoked { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    .token-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }

    .token-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        padding: 14px 16px;
        text-align: left;
        border-bottom: 2px solid #e2e8f0;
        font-size: 12.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .token-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .token-table tr:hover {
        background-color: #f8fafc;
    }

    .btn-action-icon {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s;
        text-decoration: none;
    }

    .btn-action-icon:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .btn-action-revoke {
        color: #b91c1c;
        border-color: #fecaca;
    }

    .btn-action-revoke:hover {
        background: #fef2f2;
        border-color: #f87171;
    }

    .btn-action-unbind {
        color: #0284c7;
        border-color: #bae6fd;
    }

    .btn-action-unbind:hover {
        background: #f0f9ff;
        border-color: #38bdf8;
    }

    .new-tokens-box {
        background: #f0fdf4;
        border: 2px dashed #86efac;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .gen-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        align-items: flex-end;
    }
</style>
@endsection

@section('content')

    <!-- Header Judul Halaman -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: #0f2942; margin: 0;">Panel Kontrol Token Lisensi Nutrisaka</h2>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 14px;">
                Kelola distribusi token, kunci perangkat hardware, dan buat token baru untuk permohonan dari klien ke <strong>sakanutri@gmail.com</strong>.
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.tokens.export-csv') }}" class="btn-action-icon" style="padding: 10px 16px; font-size: 13.5px;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Unduh CSV Token</span>
            </a>
        </div>
    </div>

    <!-- Kotak Token yang Baru Saja Dibuat (Flash) -->
    @if(session('generated_tokens'))
        <div class="new-tokens-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="font-weight: 800; color: #166534; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.3">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>Token Berhasil Dibuat! Segera kirimkan ke Klien:</span>
                </div>
                <button type="button" class="btn-action-icon" onclick="copyAllTokens()">
                    Salin Semua Token
                </button>
            </div>
            <div id="newTokensList" style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach(session('generated_tokens') as $newToken)
                    <div style="display: inline-flex; align-items: center; gap: 8px; background: #ffffff; border: 1.5px solid #86efac; padding: 8px 14px; border-radius: 8px;">
                        <span class="token-badge" style="font-size: 15px; background: transparent; border: none; padding: 0;">{{ $newToken }}</span>
                        <button type="button" class="btn-action-icon" onclick="copyText('{{ $newToken }}')" title="Salin Token">
                            Salin
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Kartu Statistik -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Token Diterbitkan</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon active">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="stat-info">
                <h3 style="color: #1d4ed8;">{{ $stats['active'] }}</h3>
                <p>Token Aktif Digunakan</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon unused">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/>
                </svg>
            </div>
            <div class="stat-info">
                <h3 style="color: #15803d;">{{ $stats['unused'] }}</h3>
                <p>Token Belum Digunakan</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon revoked">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div class="stat-info">
                <h3 style="color: #b91c1c;">{{ $stats['revoked'] }}</h3>
                <p>Token Dicabut (Blokir)</p>
            </div>
        </div>
    </div>

    <!-- Form Generate Token Baru -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.3">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Terbitkan Token Lisensi Baru</span>
            </h3>
            <span style="font-size: 13px; color: #64748b;">1 Token = 1 Perangkat Fisik &bull; Berlaku Selamanya</span>
        </div>
        <div class="admin-card-body">
            <form action="{{ route('admin.tokens.store') }}" method="POST">
                @csrf
                <div class="gen-grid">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                            Jumlah Token yang Dibuat
                        </label>
                        <select name="count" class="form-select" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                            <option value="1">1 Token (Untuk 1 Perangkat)</option>
                            <option value="2">2 Token</option>
                            <option value="3">3 Token</option>
                            <option value="5">5 Token (Paket Multi Perangkat)</option>
                            <option value="10">10 Token</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                            Nama Usaha Klien (Opsional)
                        </label>
                        <input type="text" name="supplier_name" class="form-input" placeholder="Contoh: CV Mandiri Pangan Sejahtera" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                            Email / WhatsApp Pemesan (Opsional)
                        </label>
                        <input type="text" name="contact" class="form-input" placeholder="Contoh: klien@gmail.com / 0812-xxx" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                            Catatan Khusus
                        </label>
                        <input type="text" name="notes" class="form-input" placeholder="Contoh: Lisensi Tambahan Android / Kasir 2" style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 11px 18px; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.3">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <span>Buat Token Lisensi</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Token Lisensi -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                <span>Daftar Token Lisensi Terdaftar ({{ $tokens->total() }})</span>
            </h3>

            <!-- Filter & Search Toolbar -->
            <form action="{{ route('admin.tokens.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari token, klien, hardware ID..." class="form-input" style="padding: 7px 12px; font-size: 13px; min-width: 240px; border-radius: 8px;">

                <select name="status" class="form-select" style="padding: 7px 12px; font-size: 13px; border-radius: 8px;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="unused" {{ request('status') === 'unused' ? 'selected' : '' }}>Belum Digunakan</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif Terikat</option>
                    <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Dicabut / Diblokir</option>
                </select>

                <button type="submit" class="btn btn-navy" style="padding: 7px 14px; font-size: 13px;">Cari</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.tokens.index') }}" class="btn btn-white" style="padding: 7px 12px; font-size: 13px;">Reset</a>
                @endif
            </form>
        </div>

        <div style="overflow-x: auto;">
            <table class="token-table">
                <thead>
                    <tr>
                        <th style="width: 210px;">Token Lisensi</th>
                        <th style="width: 120px;">Status</th>
                        <th>Perangkat Terikat (1 Perangkat)</th>
                        <th>Data Klien / Supplier</th>
                        <th style="width: 150px;">Waktu Aktivasi</th>
                        <th style="text-align: right; width: 230px;">Tindakan Administrator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tokens as $t)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="token-badge">{{ $t->token }}</span>
                                    <button type="button" class="btn-action-icon" onclick="copyText('{{ $t->token }}')" title="Salin Kode Token" style="padding: 3px 7px;">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                        </svg>
                                    </button>
                                </div>
                                @if($t->notes)
                                    <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">{{ $t->notes }}</div>
                                @endif
                            </td>

                            <td>
                                @if($t->status === 'active')
                                    <span class="status-pill active">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #2563eb;"></span>
                                        Aktif
                                    </span>
                                @elseif($t->status === 'unused')
                                    <span class="status-pill unused">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #16a34a;"></span>
                                        Ready
                                    </span>
                                @else
                                    <span class="status-pill revoked">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #dc2626;"></span>
                                        Dicabut
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($t->device_fingerprint)
                                    <div style="font-weight: 700; color: #0f2942; font-size: 13.5px;">
                                        {{ $t->device_name ?: 'Perangkat Terdaftar' }}
                                    </div>
                                    <div style="font-family: monospace; font-size: 11.5px; color: #0284c7; margin-top: 2px;" title="{{ $t->device_fingerprint }}">
                                        ID: {{ $t->short_fingerprint ?: substr($t->device_fingerprint, 0, 14) . '...' }}
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-style: italic; font-size: 12.5px;">
                                        Belum terikat perangkat (Bebas aktivasi)
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($t->supplier_name)
                                    <strong style="color: #0f2942; font-size: 13.5px;">{{ $t->supplier_name }}</strong>
                                    @if($t->pic_name)
                                        <div style="font-size: 12px; color: #475569;">PIC: {{ $t->pic_name }}</div>
                                    @endif
                                    @if($t->contact)
                                        <div style="font-size: 11.5px; color: #0284c7;">Kontak: {{ $t->contact }}</div>
                                    @endif
                                @else
                                    <span style="color: #94a3b8; font-size: 12.5px;">-</span>
                                @endif
                            </td>

                            <td>
                                @if($t->activated_at)
                                    <div style="font-weight: 600; color: #334155; font-size: 12.5px;">
                                        {{ $t->activated_at->format('d M Y') }}
                                    </div>
                                    <div style="font-size: 11px; color: #64748b;">
                                        {{ $t->activated_at->format('H:i') }} WIB (Selamanya)
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-size: 12.5px;">Belum aktif</span>
                                @endif
                            </td>

                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end; flex-wrap: wrap;">
                                    <!-- Tombol Kirim Email Balasan -->
                                    <button type="button" class="btn-action-icon" onclick="openEmailModal('{{ $t->token }}', '{{ $t->supplier_name }}', '{{ $t->contact }}')" title="Kirim/Salin Email Balasan ke Klien">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                                        </svg>
                                        <span>Balas Email</span>
                                    </button>

                                    <!-- Reset Perangkat / Unbind (jika klien ganti laptop/HP) -->
                                    @if($t->device_fingerprint)
                                        <form action="{{ route('admin.tokens.unbind', $t->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Lepas ikatan perangkat untuk token {{ $t->token }}? Token akan dapat didaftarkan kembali di perangkat baru.')">
                                            @csrf
                                            <button type="submit" class="btn-action-icon btn-action-unbind" title="Lepas ikatan perangkat jika klien ganti laptop/komputer">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M2.5 2v6h6M21.5 22v-6h-6"/><path d="M22 11.5A10 10 0 0 0 3.2 7.2M2 12.5a10 10 0 0 0 18.8 4.2"/>
                                                </svg>
                                                <span>Reset Perangkat</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Cabut / Pulihkan Token -->
                                    @if($t->status === 'revoked')
                                        <form action="{{ route('admin.tokens.restore', $t->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Pulihkan kembali token {{ $t->token }}?')">
                                            @csrf
                                            <button type="submit" class="btn-action-icon" style="color: #15803d; border-color: #86efac;" title="Buka blokir token">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"/>
                                                </svg>
                                                <span>Pulihkan</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.tokens.revoke', $t->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin MENCABUT lisensi token {{ $t->token }}? Perangkat yang menggunakan token ini akan langsung terkunci!')">
                                            @csrf
                                            <button type="submit" class="btn-action-icon btn-action-revoke" title="Cabut lisensi token ini">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                                </svg>
                                                <span>Cabut</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Hapus Token jika belum pernah terpakai -->
                                    @if($t->status === 'unused')
                                        <form action="{{ route('admin.tokens.destroy', $t->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus permanen token {{ $t->token }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon" style="color: #94a3b8;" title="Hapus token">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px; color: #94a3b8;">
                                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 12px; color: #cbd5e1;">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <div style="font-size: 15px; font-weight: 600; color: #475569;">Belum Ada Token Lisensi yang Sesuai</div>
                                <p style="margin: 4px 0 0 0; font-size: 13px;">Gunakan formulir di atas untuk menerbitkan token baru bagi klien Nutrisaka.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tokens->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0;">
                {{ $tokens->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Draf Balasan Email -->
    <div id="emailModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; border-radius: 16px; max-width: 600px; width: 100%; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25); overflow: hidden;">
            <div style="background: linear-gradient(135deg, #0f2942 0%, #0284c7 100%); color: #ffffff; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
                <h4 style="margin: 0; font-size: 17px; font-weight: 700;">Format Balasan Email Resmi ke Klien</h4>
                <button type="button" onclick="closeEmailModal()" style="background: transparent; border: none; color: #ffffff; font-size: 20px; cursor: pointer;">&times;</button>
            </div>
            <div style="padding: 24px;">
                <p style="margin: 0 0 14px 0; font-size: 13.5px; color: #475569;">
                    Salin teks berikut untuk membalas permohonan token dari klien yang mengirim ke <strong>sakanutri@gmail.com</strong>:
                </p>

                <textarea id="emailReplyText" rows="10" class="form-textarea" style="width: 100%; font-family: monospace; font-size: 13px; line-height: 1.5; padding: 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; margin-bottom: 16px;" readonly></textarea>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-white" onclick="closeEmailModal()">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="copyReplyTemplate()">
                        Salin Teks Balasan Email
                    </button>
                    <a id="mailtoLinkBtn" href="#" class="btn btn-navy" target="_blank">
                        Buka Aplikasi Email
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Token lisensi berhasil disalin:\n' + text);
        }).catch(() => {
            prompt('Salin token lisensi ini:', text);
        });
    }

    function copyAllTokens() {
        const badges = document.querySelectorAll('#newTokensList .token-badge');
        const tokens = Array.from(badges).map(b => b.textContent.trim()).join('\n');
        copyText(tokens);
    }

    function openEmailModal(token, supplier, contact) {
        const modal = document.getElementById('emailModal');
        const textarea = document.getElementById('emailReplyText');
        const mailtoBtn = document.getElementById('mailtoLinkBtn');

        const clientName = supplier || 'Bapak/Ibu Pimpinan Supplier';
        const targetEmail = (contact && contact.includes('@')) ? contact : '';

        const body = `Yth. ${clientName},

Terima kasih atas permohonan token lisensi Nutrisaka — Manajemen Supplier SPPG.

Berikut adalah Token Lisensi Resmi untuk perangkat Anda:
==================================================
KODE TOKEN : ${token}
MASA AKTIF : SELAMANYA (LIFETIME)
KETENTUAN  : 1 Token berlaku untuk 1 Perangkat Fisik
==================================================

Cara Aktivasi:
1. Buka aplikasi Nutrisaka di perangkat Anda.
2. Pada layar registrasi/login, masukkan Kode Token di atas beserta data usaha Anda.
3. Klik tombol "Aktivasi & Mulai Gunakan Aplikasi".
4. Setelah terdaftar, aplikasi dapat digunakan secara penuh 100% offline.

Jika Anda memerlukan token tambahan untuk perangkat lain (misal kasir gudang atau tablet android), silakan hubungi kami kembali di sakanutri@gmail.com.

Hormat kami,
Tim Lisensi Nutrisaka SPPG
Email: sakanutri@gmail.com`;

        textarea.value = body;

        const subject = encodeURIComponent(`[Resmi] Token Lisensi Nutrisaka SPPG - ${clientName}`);
        const mailtoUri = `mailto:${targetEmail}?subject=${subject}&body=${encodeURIComponent(body)}`;
        mailtoBtn.href = mailtoUri;

        modal.style.display = 'flex';
    }

    function closeEmailModal() {
        document.getElementById('emailModal').style.display = 'none';
    }

    function copyReplyTemplate() {
        const textarea = document.getElementById('emailReplyText');
        textarea.select();
        navigator.clipboard.writeText(textarea.value).then(() => {
            alert('Teks draf balasan email berhasil disalin ke clipboard!');
        });
    }
</script>
@endsection
