<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CredentialVault;
use App\Models\ProjectMilestone;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function show($id)
    {
        $invoice = Invoice::with(['order.client.user', 'order.package', 'payments'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Instant Payment Simulation (for testing, grading & demos)
     */
    public function simulate(Request $request, $id)
    {
        $invoice = Invoice::with('order')->findOrFail($id);

        if ($invoice->isPaid()) {
            return back()->with('info', 'Faktur invoice ini sudah berstatus LUNAS.');
        }

        $channel = $request->input('payment_channel', 'QRIS');
        $refCode = 'PAY-' . strtoupper(Str::random(10));

        // Create Payment
        Payment::create([
            'invoice_id' => $invoice->id,
            'payment_channel' => $channel,
            'reference_code' => $refCode,
            'amount_paid' => $invoice->amount,
            'paid_at' => now(),
            'status' => 'success',
            'gateway_payload' => ['mode' => 'sandbox_simulation', 'channel' => $channel, 'ref' => $refCode],
        ]);

        // Mark Invoice as PAID
        $invoice->update([
            'status' => 'PAID',
            'paid_at' => now(),
        ]);

        $order = $invoice->order;

        // If Termin 1 is paid:
        if ($invoice->term_type === 'TERM_1_DP') {
            if ($order->status === 'WAITING_DP') {
                $order->update(['status' => 'BRIEF_PHASE', 'start_date' => now()]);
            }
            return redirect()->route('briefs.create', $order->id)
                ->with('success', 'Pembayaran DP (Termin 1) berhasil diverifikasi! Silakan lanjutkan dengan mengisi brief kebutuhan website Anda.');
        }

        // If Termin 2 (Settlement) is paid:
        if ($invoice->term_type === 'TERM_2_SETTLEMENT') {
            $order->update(['status' => 'COMPLETED']);

            // Unlock Credential Vaults
            CredentialVault::where('order_id', $order->id)->update(['is_unlocked' => true]);

            // Complete Final Milestone 5
            $m5 = ProjectMilestone::where('order_id', $order->id)->where('stage_number', 5)->first();
            if ($m5) {
                $m5->update(['status' => 'APPROVED', 'approved_at' => now()]);
            }

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pelunasan (Termin 2) berhasil! Seluruh akses kredensial website Anda pada Vault telah terbuka dan Berita Acara Serah Terima (BAST) telah diterbitkan.');
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Pembayaran invoice ' . $invoice->invoice_number . ' berhasil dikonfirmasi.');
    }

    /**
     * Idempotent Payment Webhook Listener per SRS FR-PAY-02
     */
    public function webhook(Request $request)
    {
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');
        $transactionStatus = $request->input('transaction_status');

        $serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', 'default_mock_key'));
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        // In production, enforce signature validation
        if (env('APP_ENV') === 'production' && $signatureKey !== $expectedSignature) {
            return response()->json(['message' => 'Invalid signature key'], 401);
        }

        $invoice = Invoice::where('invoice_number', $orderId)->first();
        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        // Idempotency check: if already paid, return 200 OK without re-processing
        if ($invoice->status === 'PAID') {
            return response()->json(['message' => 'Invoice already paid (idempotent duplicate skipped)'], 200);
        }

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $channel = $request->input('payment_type', 'VA_PAYMENT');
            $refCode = $request->input('transaction_id', 'TRX-' . Str::random(8));

            Payment::create([
                'invoice_id' => $invoice->id,
                'payment_channel' => $channel,
                'reference_code' => $refCode,
                'amount_paid' => $invoice->amount,
                'paid_at' => now(),
                'status' => 'success',
                'gateway_payload' => $request->all(),
            ]);

            $invoice->update([
                'status' => 'PAID',
                'paid_at' => now(),
            ]);

            $order = $invoice->order;

            if ($invoice->term_type === 'TERM_1_DP') {
                $order->update(['status' => 'BRIEF_PHASE', 'start_date' => now()]);
            } elseif ($invoice->term_type === 'TERM_2_SETTLEMENT') {
                $order->update(['status' => 'COMPLETED']);
                CredentialVault::where('order_id', $order->id)->update(['is_unlocked' => true]);
                $m5 = ProjectMilestone::where('order_id', $order->id)->where('stage_number', 5)->first();
                if ($m5) $m5->update(['status' => 'APPROVED', 'approved_at' => now()]);
            }

            return response()->json(['message' => 'Payment settled successfully'], 200);
        }

        return response()->json(['message' => 'Webhook received'], 200);
    }
}
