<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use App\Models\Sppg;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'piutang'); // 'piutang' or 'riwayat'

        // Data Piutang Berjalan (Faktur Belum Lunas)
        $debtQuery = Sale::with('sppg')
            ->where('status', 'selesai')
            ->where('remaining_balance', '>', 0)
            ->latest('due_date')
            ->latest('sale_date');

        if ($sppgId = $request->input('sppg_id')) {
            $debtQuery->where('sppg_id', $sppgId);
        }

        if ($search = $request->input('search')) {
            $debtQuery->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('sppg', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $debtSales = $debtQuery->paginate(15, ['*'], 'debt_page')->withQueryString();

        // Riwayat Pembayaran Masuk
        $paymentsQuery = Payment::with('sale.sppg')->latest('payment_date')->latest('id');

        if ($sppgId) {
            $paymentsQuery->whereHas('sale', function ($sq) use ($sppgId) {
                $sq->where('sppg_id', $sppgId);
            });
        }

        $payments = $paymentsQuery->paginate(15, ['*'], 'pay_page')->withQueryString();

        // Ringkasan Finansial Piutang
        $totalOutstandingDebt = Sale::where('status', 'selesai')->sum('remaining_balance');
        $unpaidInvoicesCount = Sale::where('status', 'selesai')->where('remaining_balance', '>', 0)->count();
        $paymentsReceivedThisMonth = Payment::whereBetween('payment_date', [Carbon::now()->startOfMonth(), Carbon::now()])->sum('amount');

        $sppgs = Sppg::where('is_active', true)->orderBy('name')->get();

        return view('payments.index', compact(
            'tab',
            'debtSales',
            'payments',
            'totalOutstandingDebt',
            'unpaidInvoicesCount',
            'paymentsReceivedThisMonth',
            'sppgs'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:tunai,transfer,lainnya',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $sale = Sale::findOrFail($validated['sale_id']);

        if ($sale->status === 'batal') {
            return back()->with('error', 'Tidak dapat mencatat pembayaran pada faktur yang telah dibatalkan.');
        }

        if ($validated['amount'] > $sale->remaining_balance) {
            return back()->with('error', 'Jumlah pembayaran tidak boleh melebihi sisa piutang (Rp ' . number_format($sale->remaining_balance, 0, ',', '.') . ').');
        }

        $payment = $this->paymentService->recordPayment(
            sale: $sale,
            amount: (float) $validated['amount'],
            method: $validated['payment_method'],
            reference: $validated['reference_number'] ?? null,
            notes: $validated['notes'] ?? null,
            date: $validated['payment_date']
        );

        return back()->with('success', "Pembayaran sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " berhasil dicatat untuk Faktur {$sale->invoice_number}.");
    }
}
