<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator — Panel Lisensi Nutrisaka</title>
    <link rel="stylesheet" href="{{ asset('css/nutrisaka.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #091a2b 0%, #0f2942 50%, #0369a1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(9, 26, 43, 0.55);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #0f2942 0%, #1e3a8a 70%, #0284c7 100%);
            padding: 32px 28px;
            text-align: center;
            color: #ffffff;
            border-bottom: 4px solid var(--sky-400);
        }

        .login-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 2px solid rgba(255, 255, 255, 0.35);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            color: #ffffff;
        }

        .login-header h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin: 0;
            color: #ffffff;
        }

        .login-header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            color: var(--sky-200);
            font-weight: 500;
        }

        .login-body {
            padding: 32px 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13.5px;
            font-weight: 700;
            color: #0f2942;
            margin-bottom: 8px;
        }

        .form-input-wrap {
            position: relative;
        }

        .form-input-wrap svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .form-input-admin {
            width: 100%;
            box-sizing: border-box;
            padding: 13px 14px 13px 44px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14.5px;
            font-family: inherit;
            color: #0f2942;
            transition: all 0.2s;
            outline: none;
        }

        .form-input-admin:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .btn-submit-admin {
            width: 100%;
            background: linear-gradient(135deg, #0284c7 0%, #0f2942 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 14px 20px;
            font-size: 15.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
            margin-top: 10px;
        }

        .btn-submit-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(2, 132, 199, 0.5);
            background: linear-gradient(135deg, #0369a1 0%, #0c192c 100%);
        }

        .login-footer-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .login-footer-link a {
            color: #0284c7;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .login-footer-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="login-icon">
            <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h1>PANEL PEMILIK APLIKASI</h1>
        <p>Otentikasi Administrator Pengaturan Lisensi</p>
    </div>

    <div class="login-body">
        @if(session('error'))
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div style="background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1; padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;">
                @foreach($errors->all() as $error)
                    <div>&bull; {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Administrator</label>
                <div class="form-input-wrap">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <input type="email" name="email" id="email" class="form-input-admin" placeholder="sakanutri@gmail.com" value="{{ old('email', 'sakanutri@gmail.com') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi (Password)</label>
                <div class="form-input-wrap">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <input type="password" name="password" id="password" class="form-input-admin" placeholder="Masukkan kata sandi admin" value="admin123" required>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #475569;">
                    <input type="checkbox" name="remember" value="1" checked>
                    <span>Ingat sesi saya</span>
                </label>
                <span style="color: #64748b; font-size: 12px;">Akun: sakanutri@gmail.com</span>
            </div>

            <button type="submit" class="btn-submit-admin">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.3">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                <span>Masuk ke Panel Admin</span>
            </button>
        </form>

        <div class="login-footer-link">
            <a href="{{ route('login') }}">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                <span>Kembali ke Halaman Registrasi Token Supplier</span>
            </a>
        </div>
    </div>
</div>

</body>
</html>
