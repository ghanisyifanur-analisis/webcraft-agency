<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\RevisionTicket;
use App\Models\Invoice;

class RevisionController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $order = Order::with('package')->findOrFail($orderId);

        $request->validate([
            'milestone_id' => 'required|exists:project_milestones,id',
            'issue_description' => 'required|string',
            'target_url' => 'nullable|url|max:255',
            'attachment_url' => 'nullable|string',
        ]);

        $maxQuota = $order->package->revision_quota ?? 2;
        $isPaid = false;
        $message = 'Tiket revisi berhasil diajukan! Tim teknis akan segera menindaklanjuti.';

        if ($order->revisions_used >= $maxQuota) {
            $isPaid = true;
            // Generate invoice add-on revisi Rp 200.000 per SRS FR-REV-01
            $invoiceNumber = 'INV-' . date('Ym') . '-' . substr($order->order_number, -4) . '-REV-' . ($order->revisions_used + 1);
            Invoice::create([
                'invoice_number' => $invoiceNumber,
                'order_id' => $order->id,
                'term_type' => 'EXTRA_REVISION',
                'amount' => 200000,
                'due_date' => now()->addDays(2),
                'status' => 'UNPAID',
            ]);
            $message = 'Kuota revisi gratis paket Anda telah habis. Tiket diterbitkan sebagai revisi tambahan berbayar (Rp 200.000) dan invoice tagihan telah diterbitkan.';
        } else {
            $order->increment('revisions_used');
        }

        $ticketCount = RevisionTicket::where('order_id', $order->id)->count() + 1;
        $ticketNumber = 'REV-' . substr($order->order_number, -4) . '-' . str_pad((string)$ticketCount, 2, '0', STR_PAD_LEFT);

        RevisionTicket::create([
            'order_id' => $order->id,
            'milestone_id' => $request->milestone_id,
            'ticket_number' => $ticketNumber,
            'issue_description' => $request->issue_description,
            'target_url' => $request->target_url,
            'attachment_url' => $request->attachment_url,
            'status' => 'open',
            'is_paid_addon' => $isPaid,
        ]);

        return back()->with('success', $message);
    }

    public function update(Request $request, $id)
    {
        $ticket = RevisionTicket::findOrFail($id);

        $request->validate([
            'status' => 'required|in:open,approved_for_work,in_progress,resolved,rejected',
            'developer_response' => 'nullable|string',
        ]);

        $ticket->update([
            'status' => $request->status,
            'developer_response' => $request->developer_response ?? $ticket->developer_response,
        ]);

        return back()->with('success', 'Status tiket revisi ' . $ticket->ticket_number . ' berhasil diperbarui.');
    }
}
