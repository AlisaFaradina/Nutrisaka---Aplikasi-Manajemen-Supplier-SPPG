<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel Token Lisensi') — Nutrisaka</title>
    <link rel="stylesheet" href="{{ asset('css/nutrisaka.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --admin-navy-dark: #091a2b;
            --admin-navy: #0f2942;
            --admin-navy-light: #1e3a8a;
            --admin-sky: #0284c7;
            --admin-sky-light: #38bdf8;
            --admin-sky-subtle: #e0f2fe;
            --admin-bg: #f1f5f9;
        }

        body {
            background-color: var(--admin-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        .admin-navbar {
            background: linear-gradient(135deg, var(--admin-navy-dark) 0%, var(--admin-navy) 60%, var(--admin-navy-light) 100%);
            border-bottom: 3px solid var(--admin-sky);
            color: #ffffff;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 16px rgba(15, 41, 66, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #ffffff;
        }

        .admin-brand-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .admin-brand-text h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.8px;
            margin: 0;
            line-height: 1.2;
            color: #ffffff;
        }

        .admin-brand-text span {
            font-size: 11.5px;
            color: var(--admin-sky-light);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
        }

        .admin-nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            color: #e2e8f0;
        }

        .admin-user-pill strong {
            color: #ffffff;
        }

        .btn-admin-nav {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-admin-nav:hover {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.4);
        }

        .admin-main {
            flex: 1;
            padding: 32px 28px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        .admin-footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 18px 28px;
            font-size: 13px;
            color: #64748b;
            text-align: center;
            margin-top: auto;
        }

        .admin-footer strong {
            color: var(--admin-navy);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Admin Top Navbar -->
    <header class="admin-navbar">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <div class="admin-brand-icon">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div class="admin-brand-text">
                <h1>NUTRISAKA ADMIN</h1>
                <span>Pusat Manajemen Lisensi & Token Registrasi</span>
            </div>
        </a>

        <div class="admin-nav-actions">
            @if(auth()->guard('admin')->check())
                <div class="admin-user-pill">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Login: <strong>{{ auth()->guard('admin')->user()->name }}</strong> ({{ auth()->guard('admin')->user()->email }})</span>
                </div>

                <a href="{{ route('dashboard') }}" class="btn-admin-nav" title="Kembali ke Aplikasi Utama Supplier SPPG" target="_blank">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    <span>Buka App Supplier</span>
                </a>

                <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-admin-nav" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <span>Keluar Admin</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-admin-nav">
                    <span>Kembali ke App Supplier</span>
                </a>
            @endif
        </div>
    </header>

    <!-- Main Container -->
    <main class="admin-main">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 24px; padding: 14px 18px; border-radius: 10px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; display: flex; align-items: center; gap: 12px;">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.3">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div style="font-weight: 600;">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 24px; padding: 14px 18px; border-radius: 10px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; display: flex; align-items: center; gap: 12px;">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.3">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div style="font-weight: 600;">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info" style="margin-bottom: 24px; padding: 14px 18px; border-radius: 10px; background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1; display: flex; align-items: center; gap: 12px;">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.3">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <div style="font-weight: 600;">{{ session('info') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Admin Footer -->
    <footer class="admin-footer">
        <div>
            <strong>Nutrisaka SPPG Supplier App</strong> &bull; Panel Pemilik Aplikasi &bull; Kontak Resmi: <strong>sakanutri@gmail.com</strong>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
