<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentService
{
    /**
     * Catat pembayaran terhadap transaksi penjualan / piutang SPPG.
     */
    public function recordPayment(
        Sale $sale,
        float $amount,
        string $method = 'transfer',
        ?string $reference = null,
        ?string $notes = null,
        ?string $date = null
    ): Payment {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Jumlah pembayaran harus lebih dari 0.');
        }

        return DB::transaction(function () use ($sale, $amount, $method, $reference, $notes, $date) {
            $payment = Payment::create([
                'payment_number' => Payment::generatePaymentNumber(),
                'sale_id' => $sale->id,
                'payment_date' => $date ?? now()->toDateString(),
                'amount' => $amount,
                'payment_method' => $method,
                'reference_number' => $reference,
                'notes' => $notes,
            ]);

            // Hitung ulang seluruh pembayaran yang telah masuk untuk transaksi ini
            $totalPaid = (float) $sale->payments()->sum('amount');
            $totalAmount = (float) $sale->total_amount;
            $remaining = max(0, $totalAmount - $totalPaid);

            $newStatus = 'belum_bayar';
            if ($totalPaid >= $totalAmount && $totalAmount > 0) {
                $newStatus = 'lunas';
            } elseif ($totalPaid > 0) {
                $newStatus = 'sebagian';
            }

            $sale->update([
                'paid_amount' => $totalPaid,
                'remaining_balance' => $remaining,
                'payment_status' => $newStatus,
            ]);

            return $payment;
        });
    }
}
