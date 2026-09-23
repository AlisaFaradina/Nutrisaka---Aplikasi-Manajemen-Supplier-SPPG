<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLicensed
{
    public function __construct(
        protected LicenseService $licenseService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Daftar nama route yang dikecualikan dari pemeriksaan lisensi
        $exemptRoutes = [
            'login',
            'login.store',
            'activation.register',
            'activation.register.store',
            'activation.register.demo',
            'activation.locked',
            'activation.revalidate',
            'settings.export-backup', // Supplier selalu berhak mengunduh backup datanya sendiri
        ];

        // Dikecualikan untuk semua route admin dan API admin
        if ($request->is('admin*') || $request->is('api/admin*')) {
            return $next($request);
        }

        $routeName = $request->route() ? $request->route()->getName() : null;

        if ($routeName && (str_starts_with($routeName, 'admin.') || in_array($routeName, $exemptRoutes, true))) {
            return $next($request);
        }

        // Pengecekan status lisensi perangkat lokal
        $verification = $this->licenseService->verifyLocal();

        // 1. Belum terdaftar -> arahkan ke halaman Login Token dari Admin
        if ($verification['status'] === 'unlicensed') {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Aplikasi belum terdaftar.',
                    'redirect' => route('login'),
                ], 403);
            }

            return redirect()->route('login')
                ->with('info', 'Selamat datang di Nutrisaka! Silakan login menggunakan token lisensi resmi dari Admin terlebih dahulu.');
        }

        // 2. Terkunci (grace period habis atau token dicabut)
        if ($verification['status'] === 'locked') {
            // Jika aksi mutasi data baru, tolak dan arahkan ke layar terkunci
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Aplikasi dalam mode terkunci. Transaksi baru tidak dapat disimpan.',
                        'redirect' => route('activation.locked'),
                    ], 403);
                }

                return redirect()->route('activation.locked')
                    ->with('error', 'Aplikasi dalam mode terkunci. Transaksi atau perubahan data tidak diizinkan sampai lisensi divalidasi ulang.');
            }

            // Jika membuka halaman pembuatan data baru, arahkan ke layar terkunci
            $mutationPages = ['orders.create', 'sales.create', 'products.create', 'stock.create-in', 'stock.create-adjustment'];
            if ($routeName && in_array($routeName, $mutationPages, true)) {
                return redirect()->route('activation.locked')
                    ->with('error', 'Aplikasi dalam mode terkunci. Anda hanya dapat melihat data historis dan mengunduh cadangan (backup).');
            }
        }

        // Bagikan data lisensi ke seluruh view agar layout dapat menampilkan indikator status
        view()->share('currentLicense', $verification['license'] ?? null);
        view()->share('licenseStatus', $verification['status']);

        return $next($request);
    }
}
