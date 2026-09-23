<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Terkunci — Nutrisaka Supplier SPPG</title>
    <link rel="stylesheet" href="{{ asset('css/nutrisaka.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(145deg, #0c192c 0%, #132743 40%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
        }

        .lock-card {
            width: 100%;
            max-width: 580px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(12, 25, 44, 0.55);
            overflow: hidden;
            text-align: center;
        }

        .lock-header {
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 60%, #b91c1c 100%);
            color: #ffffff;
            padding: 36px 24px;
            border-bottom: 4px solid #ef4444;
        }

        .lock-icon {
            width: 68px;
            height: 68px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .lock-header h1 {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .lock-header p {
            margin: 8px 0 0 0;
            color: #fecaca;
            font-size: 14px;
        }

        .lock-body {
            padding: 32px;
            text-align: left;
        }

        .alert-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .meta-list {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .meta-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .meta-item:last-child {
            border-bottom: none;
        }

        .meta-label {
            color: var(--text-sub);
            font-weight: 500;
        }

        .meta-val {
            color: var(--navy-900);
            font-weight: 700;
            font-family: monospace;
        }

        .actions-stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-check-online {
            background: linear-gradient(135deg, #0284c7 0%, #0f2942 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
            transition: all 0.2s;
        }

        .btn-check-online:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(2, 132, 199, 0.4);
        }

        .btn-backup-offline {
            background: #ffffff;
            border: 1px solid var(--border-light);
            color: var(--navy-900);
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-backup-offline:hover {
            background: #f1f5f9;
        }
    </style>
</head>
<body>

<div class="lock-card">
    <div class="lock-header">
        <div class="lock-icon">
            <svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2.3">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h1>APLIKASI TERKUNCI</h1>
        <p>Perangkat Berbeda Terdeteksi atau Lisensi Perlu Divalidasi</p>
    </div>

    <div class="lock-body">
        @if(session('error'))
            <div class="alert-box">
                <strong>Pemberitahuan:</strong> {{ session('error') }}
            </div>
        @else
            <div class="alert-box">
                Aplikasi mendeteksi perangkat fisik berbeda atau status lisensi telah dinonaktifkan. Lisensi Nutrisaka berlaku selamanya untuk satu perangkat. Jika Anda menggunakan perangkat tambahan (misal Android / Desktop), Anda memerlukan token lisensi baru. Seluruh data transaksi Anda <strong>tetap tersimpan aman</strong>.
            </div>
        @endif

        <div class="meta-list">
            <div class="meta-item">
                <span class="meta-label">ID Perangkat Ini:</span>
                <span class="meta-val">{{ $shortFingerprint }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Token Terdaftar:</span>
                <span class="meta-val">{{ $license ? $license->token_masked : 'Tidak Ditemukan' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Status Lisensi:</span>
                <span class="meta-val" style="color: #dc2626;">TERKUNCI (READ-ONLY)</span>
            </div>
        </div>

        <div class="actions-stack">
            <!-- 1. Daftarkan Token Baru -->
            <a href="{{ route('login') }}" class="btn-check-online">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <span>Masukkan Token Baru untuk Perangkat Ini</span>
            </a>

            <!-- 2. Permohonan Email ke Admin -->
            <a href="mailto:sakanutri@gmail.com?subject=Permohonan%20Token%20Baru%20Nutrisaka%20-%20Perangkat%20{{ $shortFingerprint }}&body=Halo%20Admin%20Nutrisaka%2C%0D%0A%0D%0ASaya%20ingin%20meminta%20token%20lisensi%20baru%20untuk%20perangkat%20berikut%3A%0D%0AID%20Perangkat%3A%20{{ $shortFingerprint }}%20({{ $fingerprint }})%0D%0A%0D%0ATerima%20kasih." class="btn-backup-offline" style="background: #f0fdf4; border-color: #86efac; color: #166534;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                </svg>
                <span>Ajukan Token ke Email: sakanutri@gmail.com</span>
            </a>

            <!-- 3. Unduh Backup Data -->
            <a href="{{ route('settings.export-backup') }}" class="btn-backup-offline">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Unduh Cadangan Data Lengkap (Backup JSON)</span>
            </a>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; font-size: 12px; color: var(--text-sub); border-top: 1px solid #e2e8f0; padding-top: 16px;">
            <span>Email Admin: <strong>sakanutri@gmail.com</strong></span>
            <a href="{{ route('admin.login') }}" style="color: #0284c7; font-weight: 600; text-decoration: none;">
                Login Admin &rarr;
            </a>
        </div>
    </div>
</div>

</body>
</html>
