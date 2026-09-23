<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Akses administrator tidak sah.'], 401);
            }

            return redirect()->route('admin.login')
                ->with('error', 'Silakan masuk sebagai Administrator terlebih dahulu.');
        }

        return $next($request);
    }
}
