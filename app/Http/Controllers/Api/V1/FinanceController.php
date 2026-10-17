<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    public function invoices(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);
        $user = $request->user();

        $query = DB::table('invoices')
            ->join('students', 'invoices.student_id', '=', 'students.id')
            ->leftJoin('class_enrollments', function ($join) {
                $join->on('students.id', '=', 'class_enrollments.student_id')
                    ->where('class_enrollments.status', 'active');
            })
            ->leftJoin('classes', 'class_enrollments.class_id', '=', 'classes.id')
            ->where('invoices.school_id', $schoolId)
            ->select(
                'invoices.id',
                'invoices.student_id',
                'invoices.invoice_no',
                'invoices.period',
                'invoices.due_on',
                'invoices.total_amount',
                'invoices.status',
                'students.name as student_name',
                'students.admission_no',
                'classes.name as class_name'
            );

        // Check if logged in user is a guardian (Orang Tua)
        $guardian = DB::table('guardians')->where('user_id', $user->id)->first();
        if ($guardian) {
            $childIds = DB::table('student_guardians')
                ->where('guardian_id', $guardian->id)
                ->pluck('student_id')
                ->toArray();

            if ($studentId = $request->query('student_id')) {
                $query->where('invoices.student_id', $studentId);
            } else {
                $query->whereIn('invoices.student_id', $childIds);
            }
        } else {
            if ($studentId = $request->query('student_id')) {
                $query->where('invoices.student_id', $studentId);
            }
        }

        if ($status = $request->query('status')) {
            $query->where('invoices.status', $status);
        }

        if ($period = $request->query('period')) {
            $query->where('invoices.period', $period);
        }

        $invoices = $query->orderByDesc('invoices.due_on')->paginate(20);

        return response()->json([
            'data' => $invoices->items(),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'total' => $invoices->total(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $invoice = DB::table('invoices')
            ->join('students', 'invoices.student_id', '=', 'students.id')
            ->where('invoices.school_id', $schoolId)
            ->where('invoices.id', $id)
            ->select('invoices.*', 'students.name as student_name', 'students.admission_no')
            ->first();

        if (! $invoice) {
            return response()->json(['message' => 'Tagihan tidak ditemukan.'], 404);
        }

        $items = DB::table('invoice_items')
            ->where('invoice_id', $invoice->id)
            ->get();

        $payments = DB::table('payments')
            ->where('invoice_id', $invoice->id)
            ->orderByDesc('paid_at')
            ->get();

        return response()->json([
            'data' => [
                'invoice' => $invoice,
                'items' => $items,
                'payments' => $payments,
            ],
        ]);
    }

    public function recordPayment(Request $request, string $invoiceId): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'method' => 'required|string|in:cash,transfer,manual',
            'reference' => 'nullable|string|max:100',
        ]);

        $schoolId = $this->getSchoolId($request);
        $user = $request->user();

        $invoice = DB::table('invoices')
            ->where('school_id', $schoolId)
            ->where('id', $invoiceId)
            ->first();

        if (! $invoice) {
            return response()->json(['message' => 'Tagihan tidak ditemukan.'], 404);
        }

        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Tagihan sudah lunas.'], 422);
        }

        $paymentId = (string) Str::uuid();

        DB::transaction(function () use ($paymentId, $schoolId, $invoice, $validated, $user) {
            // Record payment
            DB::table('payments')->insert([
                'id' => $paymentId,
                'school_id' => $schoolId,
                'invoice_id' => $invoice->id,
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'status' => 'completed',
                'reference' => $validated['reference'] ?? 'MANUAL-' . strtoupper(Str::random(6)),
                'paid_at' => now(),
                'recorded_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Calculate total paid
            $totalPaid = DB::table('payments')
                ->where('invoice_id', $invoice->id)
                ->where('status', 'completed')
                ->sum('amount');

            $newStatus = ($totalPaid >= $invoice->total_amount) ? 'paid' : 'partially_paid';

            DB::table('invoices')
                ->where('id', $invoice->id)
                ->update(['status' => $newStatus, 'updated_at' => now()]);

            // Audit log
            DB::table('audit_logs')->insert([
                'id' => (string) Str::uuid(),
                'school_id' => $schoolId,
                'actor_user_id' => $user->id,
                'action' => 'record_payment',
                'subject_type' => 'invoice',
                'subject_id' => $invoice->id,
                'before_payload' => json_encode(['status' => $invoice->status]),
                'after_payload' => json_encode(['status' => $newStatus, 'amount' => $validated['amount']]),
                'created_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Pembayaran berhasil dicatat.',
            'data' => [
                'payment_id' => $paymentId,
                'amount' => $validated['amount'],
            ],
        ], 201);
    }

    private function getSchoolId(Request $request): string
    {
        $headerSchoolId = $request->header('X-School-Id');
        if ($headerSchoolId) return $headerSchoolId;

        $membership = DB::table('school_memberships')
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->first();

        return $membership?->school_id ?? '';
    }
}
