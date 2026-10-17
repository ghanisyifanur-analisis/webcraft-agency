<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);
        $classId = $request->query('class_id');
        $date = $request->query('date', now()->toDateString());

        $sessionQuery = DB::table('attendance_sessions')
            ->where('school_id', $schoolId)
            ->where('date', $date);

        if ($classId) {
            $sessionQuery->where('class_id', $classId);
        }

        $session = $sessionQuery->first();

        if (! $session) {
            return response()->json([
                'data' => [
                    'session' => null,
                    'records' => [],
                ],
            ]);
        }

        $records = DB::table('attendance_records')
            ->join('students', 'attendance_records.student_id', '=', 'students.id')
            ->where('attendance_records.session_id', $session->id)
            ->select(
                'attendance_records.id',
                'attendance_records.status',
                'attendance_records.source',
                'attendance_records.marked_at',
                'students.id as student_id',
                'students.name as student_name',
                'students.admission_no'
            )
            ->get();

        return response()->json([
            'data' => [
                'session' => $session,
                'records' => $records,
            ],
        ]);
    }

    /**
     * Idempotent Kartu Pelajar QR Code scanner endpoint.
     */
    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'card_token' => 'required|string',
            'idempotency_key' => 'nullable|string',
        ]);

        $schoolId = $this->getSchoolId($request);
        $user = $request->user();

        // 1. Hash the incoming card token
        $tokenHash = hash('sha256', $validated['card_token']);

        // 2. Validate student card
        $card = DB::table('student_cards')
            ->where('school_id', $schoolId)
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->first();

        if (! $card) {
            return response()->json([
                'message' => 'Kartu Pelajar tidak valid atau telah dinonaktifkan.',
            ], 404);
        }

        // 3. Find student and active class enrollment
        $student = DB::table('students')
            ->join('class_enrollments', 'students.id', '=', 'class_enrollments.student_id')
            ->join('classes', 'class_enrollments.class_id', '=', 'classes.id')
            ->where('students.id', $card->student_id)
            ->where('class_enrollments.status', 'active')
            ->select(
                'students.id',
                'students.name',
                'students.admission_no',
                'classes.id as class_id',
                'classes.name as class_name'
            )
            ->first();

        if (! $student) {
            return response()->json([
                'message' => 'Siswa tidak memiliki enrollment kelas yang aktif.',
            ], 422);
        }

        $today = now()->toDateString();

        // 4. Find or create daily attendance session for student's class
        $session = DB::table('attendance_sessions')
            ->where('class_id', $student->class_id)
            ->where('date', $today)
            ->where('session_type', 'daily')
            ->first();

        if (! $session) {
            $sessionId = (string) Str::uuid();
            DB::table('attendance_sessions')->insert([
                'id' => $sessionId,
                'school_id' => $schoolId,
                'class_id' => $student->class_id,
                'date' => $today,
                'session_type' => 'daily',
                'created_by' => $user->id,
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $session = (object) ['id' => $sessionId, 'status' => 'open'];
        }

        // 5. Check if already marked (Idempotent scan)
        $existingRecord = DB::table('attendance_records')
            ->where('session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingRecord) {
            return response()->json([
                'message' => 'Siswa sudah tercatat hadir sebelumnya.',
                'data' => [
                    'student' => $student,
                    'attendance' => $existingRecord,
                ],
            ], 200);
        }

        // 6. Record attendance
        $recordId = (string) Str::uuid();
        DB::table('attendance_records')->insert([
            'id' => $recordId,
            'session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'hadir',
            'source' => 'qr',
            'marked_by' => $user->id,
            'marked_at' => now(),
            'idempotency_key' => $validated['idempotency_key'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Absensi berhasil dicatat via QR Kartu Pelajar.',
            'data' => [
                'record_id' => $recordId,
                'student' => $student,
                'status' => 'hadir',
                'marked_at' => now()->toIso8601String(),
            ],
        ], 201);
    }

    public function sessionRecords(Request $request, string $sessionId): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $session = DB::table('attendance_sessions')
            ->where('school_id', $schoolId)
            ->where('id', $sessionId)
            ->first();

        if (! $session) {
            return response()->json(['message' => 'Sesi tidak ditemukan.'], 404);
        }

        $records = DB::table('attendance_records')
            ->join('students', 'attendance_records.student_id', '=', 'students.id')
            ->where('attendance_records.session_id', $sessionId)
            ->select('attendance_records.*', 'students.name as student_name', 'students.admission_no')
            ->get();

        return response()->json([
            'data' => [
                'session' => $session,
                'records' => $records,
            ],
        ]);
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
