<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseToken;
use App\Services\LicenseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTokenController extends Controller
{
    /**
     * Tampilan utama Dashboard & Manajemen Token Lisensi.
     */
    public function index(Request $request)
    {
        $query = LicenseToken::query()->latest();

        // Filter status
        if ($request->filled('status') && in_array($request->status, ['unused', 'active', 'revoked'])) {
            $query->where('status', $request->status);
        }

        // Pencarian teks
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('token', 'LIKE', "%{$search}%")
                  ->orWhere('supplier_name', 'LIKE', "%{$search}%")
                  ->orWhere('pic_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact', 'LIKE', "%{$search}%")
                  ->orWhere('device_name', 'LIKE', "%{$search}%")
                  ->orWhere('device_fingerprint', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        $tokens = $query->paginate(15)->withQueryString();

        // Ringkasan Statistik
        $stats = [
            'total'   => LicenseToken::count(),
            'active'  => LicenseToken::where('status', 'active')->count(),
            'unused'  => LicenseToken::where('status', 'unused')->count(),
            'revoked' => LicenseToken::where('status', 'revoked')->count(),
        ];

        return view('admin.tokens.index', compact('tokens', 'stats'));
    }

    /**
     * Membuat satu atau beberapa token lisensi baru sekaligus.
     */
    public function store(Request $request)
    {
        $request->validate([
            'count' => ['required', 'integer', 'min:1', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
        ]);

        $count = (int) $request->input('count', 1);
        $notes = $request->input('notes');
        $supplierName = $request->input('supplier_name');
        $contact = $request->input('contact');

        $created = [];

        for ($i = 0; $i < $count; $i++) {
            // Generate token unik
            do {
                $tokenString = LicenseService::generateTokenString();
            } while (LicenseToken::where('token', $tokenString)->exists());

            $token = LicenseToken::create([
                'token' => $tokenString,
                'status' => 'unused',
                'notes' => $notes ?: 'Dibuat oleh Administrator',
                'supplier_name' => $supplierName,
                'contact' => $contact,
            ]);

            $created[] = $tokenString;
        }

        $msg = $count === 1 
            ? "Token lisensi baru berhasil dibuat: {$created[0]}"
            : "{$count} token lisensi baru berhasil dibuat.";

        return back()->with('success', $msg)->with('generated_tokens', $created);
    }

    /**
     * Mencabut / menonaktifkan token lisensi (memblokir akses perangkat).
     */
    public function revoke(LicenseToken $token)
    {
        $token->update([
            'status' => 'revoked',
            'notes' => ($token->notes ? $token->notes . ' | ' : '') . 'Dicabut oleh Admin pada ' . Carbon::now()->format('d/m/Y H:i'),
        ]);

        // Jika token yang dicabut sedang aktif di database lokal ini, ubah status ke locked
        $localLicense = License::first();
        if ($localLicense && $token->device_fingerprint && $localLicense->device_fingerprint === $token->device_fingerprint) {
            $localLicense->update(['status' => 'locked']);
        }

        return back()->with('success', "Token {$token->token} berhasil dicabut. Perangkat terkait tidak dapat lagi melakukan transaksi.");
    }

    /**
     * Memulihkan token yang dicabut agar kembali aktif atau siap digunakan.
     */
    public function restore(LicenseToken $token)
    {
        $newStatus = $token->device_fingerprint ? 'active' : 'unused';
        $token->update([
            'status' => $newStatus,
            'notes' => ($token->notes ? $token->notes . ' | ' : '') . 'Dipulihkan oleh Admin pada ' . Carbon::now()->format('d/m/Y H:i'),
        ]);

        $localLicense = License::first();
        if ($localLicense && $token->device_fingerprint && $localLicense->device_fingerprint === $token->device_fingerprint) {
            $localLicense->update(['status' => 'active']);
        }

        return back()->with('success', "Token {$token->token} berhasil dipulihkan (Status: {$newStatus}).");
    }

    /**
     * Reset Perangkat (Unbind Hardware Fingerprint).
     * Memungkinkan token yang sama dipakai kembali jika klien ganti perangkat/laptop baru.
     */
    public function unbind(LicenseToken $token)
    {
        $oldDevice = $token->device_name ?: $token->short_fingerprint;

        $token->update([
            'status' => 'unused',
            'device_fingerprint' => null,
            'device_name' => null,
            'activated_at' => null,
            'notes' => ($token->notes ? $token->notes . ' | ' : '') . "Perangkat ({$oldDevice}) dilepas oleh Admin pada " . Carbon::now()->format('d/m/Y H:i'),
        ]);

        return back()->with('success', "Ikatan perangkat untuk token {$token->token} berhasil direset. Token kini siap diaktivasi di perangkat baru.");
    }

    /**
     * Menghapus token (hanya untuk token yang belum pernah aktif / unused).
     */
    public function destroy(LicenseToken $token)
    {
        if ($token->isActive()) {
            return back()->with('error', 'Token yang sedang aktif tidak dapat dihapus langsung. Cabut (Revoke) token terlebih dahulu.');
        }

        $tokenStr = $token->token;
        $token->delete();

        return back()->with('success', "Token {$tokenStr} telah dihapus dari sistem.");
    }

    /**
     * Export data token lisensi dalam format CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $fileName = 'nutrisaka-tokens-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            
            // BOM UTF-8 untuk Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($handle, [
                'ID',
                'Token Lisensi',
                'Status',
                'Nama Usaha Supplier',
                'Penanggung Jawab (PIC)',
                'Kontak',
                'Nama Perangkat',
                'Sidik Perangkat (Hardware Fingerprint)',
                'Tanggal Dibuat',
                'Tanggal Aktivasi',
                'Catatan Admin',
            ]);

            LicenseToken::orderBy('id', 'desc')->chunk(100, function ($tokens) use ($handle) {
                foreach ($tokens as $t) {
                    fputcsv($handle, [
                        $t->id,
                        $t->token,
                        strtoupper($t->status),
                        $t->supplier_name ?: '-',
                        $t->pic_name ?: '-',
                        $t->contact ?: '-',
                        $t->device_name ?: '-',
                        $t->device_fingerprint ?: '-',
                        $t->created_at ? $t->created_at->format('Y-m-d H:i') : '-',
                        $t->activated_at ? $t->activated_at->format('Y-m-d H:i') : '-',
                        $t->notes ?: '-',
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
