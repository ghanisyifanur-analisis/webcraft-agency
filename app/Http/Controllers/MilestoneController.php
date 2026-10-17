<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectMilestone;
use App\Models\Order;

class MilestoneController extends Controller
{
    public function update(Request $request, $id)
    {
        $milestone = ProjectMilestone::findOrFail($id);

        $request->validate([
            'status' => 'required|in:LOCKED,ACTIVE,IN_REVIEW,APPROVED',
            'staging_url' => 'nullable|url|max:255',
            'description' => 'nullable|string',
        ]);

        $milestone->update([
            'status' => $request->status,
            'staging_url' => $request->staging_url ?? $milestone->staging_url,
            'description' => $request->description ?? $milestone->description,
        ]);

        // If status is set to APPROVED by staff
        if ($request->status === 'APPROVED' && !$milestone->approved_at) {
            $milestone->update([
                'approved_at' => now(),
                'approved_ip' => $request->ip(),
            ]);

            // Unlock next milestone if exists
            $nextMilestone = ProjectMilestone::where('order_id', $milestone->order_id)
                ->where('stage_number', $milestone->stage_number + 1)
                ->first();

            if ($nextMilestone && $nextMilestone->status === 'LOCKED') {
                $nextMilestone->update(['status' => 'ACTIVE']);
            }
        }

        return back()->with('success', 'Status milestone #' . $milestone->stage_number . ' berhasil diperbarui.');
    }

    public function signoff(Request $request, $id)
    {
        $milestone = ProjectMilestone::findOrFail($id);
        
        $milestone->update([
            'status' => 'APPROVED',
            'approved_at' => now(),
            'approved_ip' => $request->ip(),
        ]);

        // Unlock next milestone
        $next = ProjectMilestone::where('order_id', $milestone->order_id)
            ->where('stage_number', $milestone->stage_number + 1)
            ->first();

        if ($next) {
            $next->update(['status' => 'ACTIVE']);
        }

        // If milestone 4 was approved, prompt client to pay Termin 2
        if ($milestone->stage_number == 4) {
            $order = $milestone->order;
            $order->update(['status' => 'STAGING_REVIEW']);
            return back()->with('success', 'Fase 4 (Staging) berhasil disetujui secara digital! Silakan lakukan Pelunasan (Termin 2) untuk membuka kredensial website & serah terima BAST.');
        }

        return back()->with('success', 'Milestone berhasil disetujui digital (Signed-Off). Proyek melangkah ke tahapan berikutnya.');
    }
}
