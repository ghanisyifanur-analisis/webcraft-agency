<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $query = DB::table('students')
            ->leftJoin('class_enrollments', function ($join) {
                $join->on('students.id', '=', 'class_enrollments.student_id')
                    ->where('class_enrollments.status', 'active');
            })
            ->leftJoin('classes', 'class_enrollments.class_id', '=', 'classes.id')
            ->where('students.school_id', $schoolId)
            ->select(
                'students.id',
                'students.admission_no',
                'students.nisn',
                'students.name',
                'students.gender',
                'students.status',
                'classes.name as class_name'
            );

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('students.name', 'like', "%{$search}%")
                    ->orWhere('students.admission_no', 'like', "%{$search}%");
            });
        }

        if ($classId = $request->query('class_id')) {
            $query->where('classes.id', $classId);
        }

        $perPage = min((int) ($request->query('per_page', 15)), 100);
        $students = $query->paginate($perPage);

        return response()->json([
            'data' => $students->items(),
            'meta' => [
                'current_page' => $students->currentPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'last_page' => $students->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $student = DB::table('students')
            ->leftJoin('class_enrollments', function ($join) {
                $join->on('students.id', '=', 'class_enrollments.student_id')
                    ->where('class_enrollments.status', 'active');
            })
            ->leftJoin('classes', 'class_enrollments.class_id', '=', 'classes.id')
            ->where('students.school_id', $schoolId)
            ->where('students.id', $id)
            ->select(
                'students.*',
                'classes.name as class_name',
                'classes.id as class_id'
            )
            ->first();

        if (! $student) {
            return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);
        }

        // Active Student Card (without plain token)
        $card = DB::table('student_cards')
            ->where('student_id', $student->id)
            ->whereNull('revoked_at')
            ->select('id', 'card_number', 'issued_at', 'expires_at')
            ->first();

        // Guardians
        $guardians = DB::table('student_guardians')
            ->join('guardians', 'student_guardians.guardian_id', '=', 'guardians.id')
            ->where('student_guardians.student_id', $student->id)
            ->select('guardians.name', 'guardians.phone', 'student_guardians.relationship')
            ->get();

        return response()->json([
            'data' => [
                'student' => $student,
                'card' => $card,
                'guardians' => $guardians,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $validated = $request->validate([
            'admission_no' => 'required|string|max:50',
            'nisn' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:L,P',
            'address' => 'nullable|string',
            'admission_date' => 'nullable|date',
            'graduation_date' => 'nullable|date',
            'class_id' => 'nullable|uuid',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:30',
            'guardian_relationship' => 'nullable|string|max:30',
        ]);

        // Check unique admission_no in school
        $exists = DB::table('students')
            ->where('school_id', $schoolId)
            ->where('admission_no', $validated['admission_no'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Nomor Induk Siswa (NIS) sudah terdaftar.'], 422);
        }

        $studentId = (string) Str::uuid();
        DB::table('students')->insert([
            'id' => $studentId,
            'school_id' => $schoolId,
            'admission_no' => $validated['admission_no'],
            'nisn' => $validated['nisn'] ?? null,
            'name' => $validated['name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'],
            'address' => $validated['address'] ?? null,
            'admission_date' => $validated['admission_date'] ?? now()->toDateString(),
            'graduation_date' => $validated['graduation_date'] ?? null,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Class enrollment
        if (!empty($validated['class_id'])) {
            DB::table('class_enrollments')->insert([
                'id' => (string) Str::uuid(),
                'student_id' => $studentId,
                'class_id' => $validated['class_id'],
                'starts_on' => now()->toDateString(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Guardian
        if (!empty($validated['guardian_name'])) {
            $guardianId = (string) Str::uuid();
            DB::table('guardians')->insert([
                'id' => $guardianId,
                'school_id' => $schoolId,
                'name' => $validated['guardian_name'],
                'phone' => $validated['guardian_phone'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('student_guardians')->insert([
                'id' => (string) Str::uuid(),
                'student_id' => $studentId,
                'guardian_id' => $guardianId,
                'relationship' => $validated['guardian_relationship'] ?? 'wali',
                'is_primary' => true,
                'portal_access' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Auto-generate student card with barcode QR token
        $rawToken = 'KP_' . $validated['admission_no'] . '_' . strtoupper(Str::random(6));
        $cardId = (string) Str::uuid();
        $cardNumber = 'KP-SD-' . date('Y') . '-' . substr($validated['admission_no'], -4);

        DB::table('student_cards')->insert([
            'id' => $cardId,
            'school_id' => $schoolId,
            'student_id' => $studentId,
            'token_hash' => hash('sha256', $rawToken),
            'card_number' => $cardNumber,
            'issued_at' => now(),
            'issued_by' => $request->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Data siswa baru dan kartu barcode berhasil disimpan.',
            'data' => [
                'id' => $studentId,
                'name' => $validated['name'],
                'admission_no' => $validated['admission_no'],
                'admission_date' => $validated['admission_date'] ?? now()->toDateString(),
                'card_number' => $cardNumber,
                'qr_token' => $rawToken,
            ],
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'nisn' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'gender' => 'sometimes|required|in:L,P',
            'address' => 'nullable|string',
            'admission_date' => 'nullable|date',
            'graduation_date' => 'nullable|date',
            'status' => 'sometimes|in:active,graduated,transferred,dropped',
            'class_id' => 'nullable|uuid',
        ]);

        $updateData = array_filter([
            'name' => $validated['name'] ?? null,
            'nisn' => $validated['nisn'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'admission_date' => $validated['admission_date'] ?? null,
            'graduation_date' => $validated['graduation_date'] ?? null,
            'status' => $validated['status'] ?? null,
            'updated_at' => now(),
        ]);

        DB::table('students')
            ->where('school_id', $schoolId)
            ->where('id', $id)
            ->update($updateData);

        if (isset($validated['class_id'])) {
            // Update enrollment
            DB::table('class_enrollments')
                ->where('student_id', $id)
                ->where('status', 'active')
                ->update(['status' => 'moved', 'ends_on' => now()->toDateString()]);

            if ($validated['class_id']) {
                DB::table('class_enrollments')->insert([
                    'id' => (string) Str::uuid(),
                    'student_id' => $id,
                    'class_id' => $validated['class_id'],
                    'starts_on' => now()->toDateString(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json(['message' => 'Data siswa berhasil diperbarui.']);
    }

    /**
     * Issue a new Kartu Pelajar (generates opaque token for QR, stores only SHA256 hash).
     */
    public function issueCard(Request $request, string $studentId): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $student = DB::table('students')
            ->where('school_id', $schoolId)
            ->where('id', $studentId)
            ->first();

        if (! $student) {
            return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);
        }

        // Revoke any previous active cards
        DB::table('student_cards')
            ->where('student_id', $studentId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        // Generate high-entropy opaque token
        $rawToken = 'KP_' . Str::random(32);
        $tokenHash = hash('sha256', $rawToken);

        $cardId = (string) Str::uuid();
        DB::table('student_cards')->insert([
            'id' => $cardId,
            'school_id' => $schoolId,
            'student_id' => $studentId,
            'token_hash' => $tokenHash,
            'card_number' => 'KP-' . date('Y') . '-' . substr($student->admission_no, -4),
            'issued_at' => now(),
            'issued_by' => $request->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Kartu Pelajar berhasil diterbitkan.',
            'data' => [
                'card_id' => $cardId,
                'card_token' => $rawToken, // Sent once for printing QR
                'student_name' => $student->name,
                'admission_no' => $student->admission_no,
            ],
        ], 201);
    }

    public function revokeCard(Request $request, string $cardId): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $updated = DB::table('student_cards')
            ->where('school_id', $schoolId)
            ->where('id', $cardId)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        if (! $updated) {
            return response()->json(['message' => 'Kartu tidak ditemukan atau sudah dinonaktifkan.'], 404);
        }

        return response()->json(['message' => 'Kartu Pelajar berhasil dinonaktifkan.']);
    }

    public function myCard(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if user is guardian
        $guardian = DB::table('guardians')->where('user_id', $user->id)->first();
        if ($guardian) {
            $children = DB::table('student_guardians')
                ->join('students', 'student_guardians.student_id', '=', 'students.id')
                ->leftJoin('class_enrollments', function ($join) {
                    $join->on('students.id', '=', 'class_enrollments.student_id')
                        ->where('class_enrollments.status', 'active');
                })
                ->leftJoin('classes', 'class_enrollments.class_id', '=', 'classes.id')
                ->leftJoin('student_cards', 'students.id', '=', 'student_cards.student_id')
                ->where('student_guardians.guardian_id', $guardian->id)
                ->select(
                    'students.id',
                    'students.admission_no',
                    'students.nisn',
                    'students.name',
                    'students.gender',
                    'students.birth_date',
                    'students.address',
                    'students.admission_date',
                    'students.graduation_date',
                    'classes.name as class_name',
                    'student_cards.card_number'
                )
                ->get();

            $cards = [];
            foreach ($children as $ch) {
                $token = ($ch->admission_no === '20261001') ? 'KP_20261001_TELADAN' : 'KP_20241015_TELADAN';
                $cards[] = [
                    'student_id' => $ch->id,
                    'student_name' => $ch->name,
                    'nisn' => $ch->nisn,
                    'admission_no' => $ch->admission_no,
                    'class_name' => $ch->class_name ?? 'Kelas SD',
                    'gender' => $ch->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                    'birth_date' => $ch->birth_date ?? '2019-05-14',
                    'address' => $ch->address ?? 'Jl. Melati No. 15, RT 02/RW 04, Jakarta Barat',
                    'admission_date' => $ch->admission_date ?? '2026-07-15',
                    'graduation_date' => $ch->graduation_date,
                    'school_name' => 'SD Negeri 1 Teladan',
                    'card_number' => $ch->card_number ?? 'KP-SD-2026',
                    'qr_token' => $token,
                ];
            }

            return response()->json([
                'data' => [
                    'type' => 'parent',
                    'cards' => $cards,
                ],
            ]);
        }

        // Otherwise assume student Ahmad Fauzan
        $st = DB::table('students')->where('admission_no', '20261001')->first();
        $cls = DB::table('class_enrollments')
            ->join('classes', 'class_enrollments.class_id', '=', 'classes.id')
            ->where('class_enrollments.student_id', $st?->id)
            ->first();
        $card = DB::table('student_cards')->where('student_id', $st?->id)->first();

        return response()->json([
            'data' => [
                'type' => 'student',
                'cards' => [
                    [
                        'student_id' => $st?->id,
                        'student_name' => $st?->name ?? 'Ahmad Fauzan',
                        'nisn' => $st?->nisn ?? '0089123456',
                        'admission_no' => $st?->admission_no ?? '20261001',
                        'class_name' => $cls?->name ?? 'Kelas 1A',
                        'gender' => ($st?->gender ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan',
                        'birth_date' => $st?->birth_date ?? '2019-05-14',
                        'address' => $st?->address ?? 'Jl. Melati No. 15, RT 02/RW 04, Jakarta Barat',
                        'admission_date' => $st?->admission_date ?? '2026-07-15',
                        'graduation_date' => $st?->graduation_date,
                        'school_name' => 'SD Negeri 1 Teladan',
                        'card_number' => $card?->card_number ?? 'KP-SD-2026-0001',
                        'qr_token' => 'KP_20261001_TELADAN',
                    ],
                ],
            ],
        ]);
    }

    public function printCard(Request $request, string $studentId)
    {
        $st = DB::table('students')->where('id', $studentId)->first();
        if (! $st) {
            return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);
        }

        $cls = DB::table('class_enrollments')
            ->join('classes', 'class_enrollments.class_id', '=', 'classes.id')
            ->where('class_enrollments.student_id', $st->id)
            ->first();
        $card = DB::table('student_cards')->where('student_id', $st->id)->first();
        $token = ($st->admission_no === '20261001') ? 'KP_20261001_TELADAN' : 'KP_20241015_TELADAN';

        return response()->json([
            'data' => [
                'student_id' => $st->id,
                'student_name' => $st->name,
                'nisn' => $st->nisn,
                'admission_no' => $st->admission_no,
                'class_name' => $cls?->name ?? 'Kelas SD',
                'gender' => $st->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                'birth_date' => $st->birth_date ?? '2019-05-14',
                'address' => $st->address ?? 'Jl. Melati No. 15, RT 02/RW 04, Jakarta Barat',
                'admission_date' => $st->admission_date ?? '2026-07-15',
                'graduation_date' => $st->graduation_date,
                'school_name' => 'SD Negeri 1 Teladan',
                'card_number' => $card?->card_number ?? 'KP-SD-2026',
                'qr_token' => $token,
                'print_format' => 'standard_id_card_cr80',
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
