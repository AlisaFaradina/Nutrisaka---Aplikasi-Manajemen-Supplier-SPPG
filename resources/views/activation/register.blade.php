<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Token Lisensi — Nutrisaka Supplier SPPG</title>
    <link rel="stylesheet" href="{{ asset('css/nutrisaka.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(145deg, #0c192c 0%, #132743 40%, #0369a1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
        }

        .reg-container {
            width: 100%;
            max-width: 960px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(12, 25, 44, 0.45);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .reg-header {
            background: linear-gradient(135deg, #0f2942 0%, #1e3a8a 70%, #0284c7 100%);
            padding: 32px 40px;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid var(--sky-400);
        }

        .reg-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .reg-brand-icon {
            width: 54px;
            height: 54px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .reg-brand-title h1 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 0;
            color: #ffffff;
        }

        .reg-brand-title p {
            margin: 4px 0 0 0;
            font-size: 13px;
            color: var(--sky-200);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .network-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .network-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
        }

        .network-dot.offline {
            background: #f59e0b;
            box-shadow: 0 0 8px #f59e0b;
        }

        .reg-body {
            padding: 36px 40px;
        }

        .reg-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 32px;
        }

        .section-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 22px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 700;
            color: var(--navy-900);
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--sky-100);
        }

        .section-title svg {
            color: var(--sky-600);
        }

        .token-input-box {
            background: #ffffff;
            border: 2px solid var(--sky-500);
            border-radius: 12px;
            padding: 14px 18px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.12);
        }

        .token-input {
            width: 100%;
            font-family: 'Courier New', Courier, monospace;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 2px;
            color: var(--navy-900);
            border: none;
            outline: none;
            text-transform: uppercase;
            background: transparent;
        }

        .token-input::placeholder {
            color: #94a3b8;
            font-weight: 500;
            letter-spacing: 1px;
            font-size: 16px;
        }

        .device-info-badge {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12.5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }

        .device-info-badge strong {
            font-family: monospace;
            font-size: 13.5px;
            color: #15803d;
        }

        .btn-copy {
            background: #ffffff;
            border: 1px solid #86efac;
            color: #15803d;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-copy:hover {
            background: #dcfce7;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #0369a1;
        }

        .help-box {
            background: #f0f9ff;
            border: 1px dashed var(--sky-400);
            border-radius: 12px;
            padding: 16px;
            font-size: 13px;
            color: #0369a1;
            line-height: 1.5;
        }

        .help-box strong {
            color: var(--navy-900);
        }

        .btn-submit-reg {
            background: linear-gradient(135deg, #0284c7 0%, #0f2942 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 16px 28px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.4);
            transition: all 0.2s;
        }

        .btn-submit-reg:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(2, 132, 199, 0.5);
            background: linear-gradient(135deg, #0369a1 0%, #0c192c 100%);
        }

        .demo-bar {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #92400e;
        }

        @media (max-width: 820px) {
            .reg-grid {
                grid-template-columns: 1fr;
            }
            .reg-header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
            .reg-brand {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="reg-container">
    <!-- Header -->
    <div class="reg-header">
        <div class="reg-brand">
            <div class="reg-brand-icon">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div class="reg-brand-title">
                <h1>NUTRISAKA</h1>
                <p>REGISTRASI TOKEN & LISENSI PERANGKAT SUPPLIER SPPG</p>
            </div>
        </div>
        <div class="network-badge" id="netStatusBadge">
            <span class="network-dot" id="netStatusDot"></span>
            <span id="netStatusText">Memeriksa Koneksi...</span>
        </div>
    </div>

    <!-- Body -->
    <div class="reg-body">

        @if($isLocal)
            <div class="demo-bar">
                <div>
                    <strong>Mode Pengembangan Terdeteksi:</strong> Anda dapat mengaktifkan sistem secara instan dengan token demo tanpa server eksternal.
                </div>
                <form action="{{ route('activation.register.demo') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-navy" style="padding: 6px 14px; font-size: 12.5px;">
                        Gunakan Token Demo 1-Klik
                    </button>
                </form>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>
                    <strong>Registrasi Ditolak:</strong>
                    <div style="margin-top: 4px;">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>
                    <strong>Mohon lengkapi formulir dengan benar:</strong>
                    <ul style="margin-left: 18px; margin-top: 4px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('activation.register.store') }}" method="POST" id="regForm">
            @csrf

            <div class="reg-grid">
                <!-- Kolom Kiri: Profil Usaha Supplier & PIN -->
                <div>
                    <!-- Langkah 1: Identitas Supplier -->
                    <div class="section-card">
                        <div class="section-title">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>1. Data Identitas Usaha Supplier</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nama Perusahaan / Supplier Pangan <span class="required">*</span></label>
                            <input type="text" name="supplier_name" class="form-input" placeholder="Contoh: CV Pangan Mandiri Harapan" value="{{ old('supplier_name', $settings['supplier_name']) }}" required>
                            <div class="form-hint">Nama ini akan tampil resmi pada kepala surat faktur invoice dan struk nota.</div>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label class="form-label">Penanggung Jawab (PIC) <span class="required">*</span></label>
                                    <input type="text" name="pic_name" class="form-input" placeholder="Nama Pemilik / Direktur" value="{{ old('pic_name', 'H. Budi Santoso') }}" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label class="form-label">No. WhatsApp / Telepon <span class="required">*</span></label>
                                    <input type="text" name="supplier_phone" class="form-input" placeholder="Contoh: 0812-3456-7890" value="{{ old('supplier_phone', $settings['supplier_phone']) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Alamat Kantor / Gudang Logistik</label>
                            <textarea name="supplier_address" rows="2" class="form-textarea" placeholder="Alamat lengkap operasional supplier">{{ old('supplier_address', $settings['supplier_address']) }}</textarea>
                        </div>
                    </div>

                    <!-- Langkah 3: Keamanan PIN -->
                    <div class="section-card">
                        <div class="section-title">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span>3. PIN Keamanan Aplikasi Lokal</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Buat PIN Akses Baru (4–6 Angka) <span class="required">*</span></label>
                            <input type="password" name="pin" maxlength="6" class="form-input" placeholder="Contoh: 1234" value="{{ old('pin', '1234') }}" required style="letter-spacing: 4px; font-weight: 700;">
                            <div class="form-hint">Digunakan saat membuka menu Pengaturan, memulihkan data, atau konfirmasi tindakan penting.</div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Token Lisensi & Perangkat -->
                <div>
                    <!-- Langkah 2: Token Lisensi -->
                    <div class="section-card" style="background: #ffffff; border-color: var(--sky-200); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.06);">
                        <div class="section-title">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span>2. Token Lisensi Resmi</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Masukkan Token Lisensi <span class="required">*</span></label>
                            <div class="token-input-box">
                                <input type="text" name="token" id="tokenField" class="token-input" placeholder="NTRS-XXXX-XXXX-XXXX" value="{{ old('token') }}" required autocomplete="off" autofocus>
                            </div>
                            <div class="form-hint" style="margin-top: 6px;">Format token 16 karakter dipisahkan tanda strip.</div>
                        </div>

                        <!-- Hardware Fingerprint Info -->
                        <div class="device-info-badge">
                            <div>
                                <span style="font-size: 11px; text-transform: uppercase; color: #166534; font-weight: 600; display: block;">Perangkat Ini:</span>
                                <strong>{{ $shortFingerprint }}</strong>
                            </div>
                            <button type="button" class="btn-copy" onclick="copyFingerprint('{{ $fingerprint }}')">
                                Salin ID Lengkap
                            </button>
                        </div>
                        <input type="hidden" id="fullFingerprint" value="{{ $fingerprint }}">
                    </div>

                    <!-- Permohonan Token ke Admin -->
                    <div class="help-box" style="background: #f0fdf4; border-color: #86efac; color: #166534; margin-bottom: 18px;">
                        <div style="font-weight: 700; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; color: #15803d;">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            Belum Punya Token? Ajukan ke Admin Resmi
                        </div>
                        <p style="margin: 0 0 10px 0; font-size: 12.5px; line-height: 1.4;">
                            Untuk mendapatkan token lisensi resmi perangkat ini, kirimkan permohonan via email ke <strong>sakanutri@gmail.com</strong>.
                        </p>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button type="button" class="btn-copy" onclick="sendTokenRequestEmail()" style="background: #16a34a; color: #ffffff; border: none; padding: 6px 12px; font-size: 12px; border-radius: 6px; font-weight: 700;">
                                ✉ Kirim Email Permohonan
                            </button>
                            <button type="button" class="btn-copy" onclick="copyTokenRequestFormat()" style="background: #ffffff; border: 1px solid #86efac; color: #15803d; padding: 6px 12px; font-size: 12px; border-radius: 6px;">
                                Salin Draf Permohonan
                            </button>
                        </div>
                    </div>

                    <!-- Bantuan & Kebijakan Lisensi Permanen Per Perangkat -->
                    <div class="help-box">
                        <div style="font-weight: 700; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            Ketentuan Lisensi Nutrisaka:
                        </div>
                        <ul style="margin: 0; padding-left: 18px;">
                            <li><strong>Masa Aktif Selamanya (Lifetime):</strong> Tidak ada kedaluwarsa waktu selama tidak logout / keluar akun.</li>
                            <li><strong>1 Token = 1 Perangkat Fisik:</strong> Penggunaan pada perangkat lain (Desktop Windows vs Android) memerlukan token baru terpisah.</li>
                            <li><strong>100% Offline-First:</strong> Setelah registrasi sukses, seluruh pencatatan transaksi berjalan offline.</li>
                        </ul>
                    </div>

                    <div style="margin-top: 24px;">
                        <button type="submit" class="btn-submit-reg" id="btnSubmit">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.3">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <span>Aktivasi & Mulai Gunakan Aplikasi</span>
                        </button>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 18px; padding-top: 14px; border-top: 1px solid #e2e8f0; font-size: 12.5px; color: var(--text-sub);">
                        <span>Email Admin: <strong>sakanutri@gmail.com</strong></span>
                        <a href="{{ route('admin.login') }}" style="color: #0284c7; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span>Login Pemilik (Admin) &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Format otomatis input token lisensi
    const tokenInput = document.getElementById('tokenField');
    tokenInput.addEventListener('input', function(e) {
        let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (val.startsWith('NTRS')) {
            val = val.substring(4);
        }
        let parts = ['NTRS'];
        for (let i = 0; i < val.length && i < 12; i += 4) {
            parts.push(val.substring(i, i + 4));
        }
        e.target.value = parts.join('-');
    });

    // Salin sidik perangkat lengkap
    function copyFingerprint(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Sidik Perangkat (Hardware ID) berhasil disalin ke clipboard:\n' + text);
        }).catch(() => {
            prompt('Salin Sidik Perangkat ini secara manual:', text);
        });
    }

    // Deteksi koneksi online / offline
    function updateNetworkStatus() {
        const badge = document.getElementById('netStatusBadge');
        const dot = document.getElementById('netStatusDot');
        const text = document.getElementById('netStatusText');

        if (navigator.onLine) {
            dot.className = 'network-dot';
            text.innerText = 'Online (Siap Aktivasi)';
            badge.title = 'Perangkat terhubung ke internet. Siap memverifikasi token.';
        } else {
            dot.className = 'network-dot offline';
            text.innerText = 'Offline (Perlu Internet)';
            badge.title = 'Aktivasi token pertama kali membutuhkan koneksi internet.';
        }
    }

    window.addEventListener('online', updateNetworkStatus);
    window.addEventListener('offline', updateNetworkStatus);
    updateNetworkStatus();

    // Kirim permohonan token lisensi via mailto ke sakanutri@gmail.com
    function sendTokenRequestEmail() {
        const supplierName = document.querySelector('input[name="supplier_name"]')?.value || 'Supplier SPPG';
        const picName = document.querySelector('input[name="pic_name"]')?.value || '';
        const phone = document.querySelector('input[name="supplier_phone"]')?.value || '';
        const fp = document.getElementById('fullFingerprint')?.value || '';
        const shortFp = '{{ $shortFingerprint }}';

        const subject = encodeURIComponent(`Permohonan Token Lisensi Nutrisaka - ${supplierName}`);
        const body = encodeURIComponent(`Yth. Administrator Nutrisaka,

Saya bermaksud mengajukan permohonan Token Lisensi Resmi untuk aplikasi Nutrisaka Supplier SPPG pada perangkat kami:

DATA PEMOHON:
- Nama Perusahaan / Usaha : ${supplierName}
- Penanggung Jawab (PIC)   : ${picName}
- No. WhatsApp / Telepon   : ${phone}
- ID Hardware Perangkat    : ${shortFp}
- Sidik Perangkat Penuh    : ${fp}
- Jenis Perangkat          : Komputer Desktop / Laptop / Android

Mohon kiranya dapat diterbitkan kode token lisensi beserta petunjuk pembayarannya.

Terima kasih.
Hormat saya,
${picName}`);

        window.location.href = `mailto:sakanutri@gmail.com?subject=${subject}&body=${body}`;
    }

    // Salin format draf permohonan email ke clipboard
    function copyTokenRequestFormat() {
        const supplierName = document.querySelector('input[name="supplier_name"]')?.value || '[Nama Usaha Anda]';
        const picName = document.querySelector('input[name="pic_name"]')?.value || '[Nama Anda]';
        const phone = document.querySelector('input[name="supplier_phone"]')?.value || '[No WhatsApp]';
        const fp = document.getElementById('fullFingerprint')?.value || '';
        const shortFp = '{{ $shortFingerprint }}';

        const text = `Kepada: sakanutri@gmail.com
Subjek: Permohonan Token Lisensi Nutrisaka - ${supplierName}

Yth. Administrator Nutrisaka,

Saya bermaksud mengajukan permohonan Token Lisensi Resmi untuk aplikasi Nutrisaka Supplier SPPG pada perangkat kami:

DATA PEMOHON:
- Nama Perusahaan / Usaha : ${supplierName}
- Penanggung Jawab (PIC)   : ${picName}
- No. WhatsApp / Telepon   : ${phone}
- ID Hardware Perangkat    : ${shortFp}
- Sidik Perangkat Penuh    : ${fp}
- Jenis Perangkat          : Komputer Desktop / Laptop / Android

Mohon kiranya dapat diterbitkan kode token lisensi beserta petunjuk pembayarannya.

Terima kasih.
Hormat saya,
${picName}`;

        navigator.clipboard.writeText(text).then(() => {
            alert('Format email permohonan token berhasil disalin ke clipboard!\n\nSilakan buka aplikasi email atau browser Anda dan kirimkan ke sakanutri@gmail.com.');
        }).catch(() => {
            prompt('Salin teks permohonan email ini:', text);
        });
    }
</script>

</body>
</html>
