<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get user active school
        $membership = DB::table('school_memberships')
            ->join('schools', 'school_memberships.school_id', '=', 'schools.id')
            ->where('school_memberships.user_id', $user->id)
            ->where('school_memberships.status', 'active')
            ->select('schools.id as school_id', 'schools.name as school_name')
            ->first();

        $schoolId = $membership?->school_id;

        // Total active students
        $totalStudents = DB::table('students')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        // Today's attendance summary
        $today = now()->toDateString();
        $attendanceCount = DB::table('attendance_records')
            ->join('attendance_sessions', 'attendance_records.session_id', '=', 'attendance_sessions.id')
            ->where('attendance_sessions.school_id', $schoolId)
            ->where('attendance_sessions.date', $today)
            ->selectRaw('count(case when attendance_records.status = "hadir" then 1 end) as hadir')
            ->selectRaw('count(case when attendance_records.status = "izin" then 1 end) as izin')
            ->selectRaw('count(case when attendance_records.status = "sakit" then 1 end) as sakit')
            ->selectRaw('count(case when attendance_records.status = "alpa" then 1 end) as alpa')
            ->first();

        // Pending invoices
        $unpaidInvoicesCount = DB::table('invoices')
            ->where('school_id', $schoolId)
            ->where('status', 'unpaid')
            ->count();

        // Recent announcements
        $recentAnnouncements = DB::table('announcements')
            ->where('school_id', $schoolId)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'title', 'published_at']);

        return response()->json([
            'data' => [
                'school' => [
                    'id' => $membership?->school_id,
                    'name' => $membership?->school_name,
                    'academic_period' => '2026/2027 · Semester Ganjil',
                ],
                'summary' => [
                    'total_students' => $totalStudents,
                    'attendance_today' => [
                        'hadir' => (int) ($attendanceCount?->hadir ?? 0),
                        'izin' => (int) ($attendanceCount?->izin ?? 0),
                        'sakit' => (int) ($attendanceCount?->sakit ?? 0),
                        'alpa' => (int) ($attendanceCount?->alpa ?? 0),
                    ],
                    'unpaid_invoices_count' => $unpaidInvoicesCount,
                ],
                'announcements' => $recentAnnouncements,
            ],
            'meta' => [
                'request_id' => (string) str()->uuid(),
            ],
        ]);
    }
}
