<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\BriefSubmission;
use App\Models\ProjectMilestone;
use Illuminate\Support\Str;

class BriefController extends Controller
{
    public function create($orderId)
    {
        $order = Order::with(['package', 'brief'])->findOrFail($orderId);
        
        if (!$order->isDpPaid() && $order->status === 'WAITING_DP') {
            return redirect()->route('invoices.show', $order->dpInvoice->id ?? 1)
                ->with('error', 'Silakan selesaikan pembayaran DP Termin 1 terlebih dahulu sebelum mengisi brief proyek.');
        }

        return view('briefs.create', compact('order'));
    }

    public function store(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'brand_name' => 'required|string|max:120',
            'tagline_description' => 'nullable|string',
            'color_palette' => 'nullable|string|max:100',
            'target_audience' => 'nullable|string',
            'competitor_references' => 'nullable|string',
            'logo_file' => 'nullable|file|mimes:png,jpg,jpeg,svg,zip,pdf|max:25600', // max 25MB
            'supporting_assets_url' => 'nullable|string',
            'feature_notes' => 'nullable|string',
        ]);

        $logoPath = $order->brief->logo_file_path ?? null;
        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $logoName = time() . '_' . Str::slug($request->brand_name) . '.' . $file->extension();
            $file->move(public_path('uploads/logos'), $logoName);
            $logoPath = 'uploads/logos/' . $logoName;
        }

        $brief = BriefSubmission::updateOrCreate(
            ['order_id' => $order->id],
            [
                'brand_name' => $request->brand_name,
                'tagline_description' => $request->tagline_description,
                'color_palette' => $request->color_palette,
                'target_audience' => $request->target_audience,
                'competitor_references' => $request->competitor_references,
                'logo_file_path' => $logoPath,
                'supporting_assets_url' => $request->supporting_assets_url,
                'feature_notes' => $request->feature_notes,
                'status' => 'SUBMITTED',
            ]
        );

        $order->update(['status' => 'BRIEF_PHASE']);

        // Activate Milestone 1
        $m1 = ProjectMilestone::where('order_id', $order->id)->where('stage_number', 1)->first();
        if ($m1 && $m1->status === 'LOCKED') {
            $m1->update(['status' => 'ACTIVE']);
        }

        return redirect()->route('orders.show', $order->id)->with('success', 'Brief proyek berhasil diserahkan! Project Manager akan segera meninjau materi Anda.');
    }

    public function review(Request $request, $briefId)
    {
        $request->validate([
            'status' => 'required|in:ACCEPTED,NEED_REVISION',
            'pm_feedback' => 'nullable|string',
        ]);

        $brief = BriefSubmission::findOrFail($briefId);
        $brief->update([
            'status' => $request->status,
            'pm_feedback' => $request->pm_feedback,
            'reviewed_at' => now(),
        ]);

        if ($request->status === 'ACCEPTED') {
            $order = $brief->order;
            $order->update(['status' => 'IN_PROGRESS']);

            // Complete Milestone 1 & Activate Milestone 2
            $m1 = ProjectMilestone::where('order_id', $order->id)->where('stage_number', 1)->first();
            if ($m1) {
                $m1->update(['status' => 'APPROVED', 'approved_at' => now()]);
            }

            $m2 = ProjectMilestone::where('order_id', $order->id)->where('stage_number', 2)->first();
            if ($m2) {
                $m2->update(['status' => 'ACTIVE']);
            }
        }

        return back()->with('success', 'Peninjauan brief berhasil diperbarui.');
    }
}
