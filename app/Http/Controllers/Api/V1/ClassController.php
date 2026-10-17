<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $classes = DB::table('classes')
            ->leftJoin('school_memberships', 'classes.homeroom_membership_id', '=', 'school_memberships.id')
            ->leftJoin('users', 'school_memberships.user_id', '=', 'users.id')
            ->where('classes.school_id', $schoolId)
            ->select(
                'classes.id',
                'classes.name',
                'classes.grade_level',
                'users.name as homeroom_teacher_name'
            )
            ->get();

        return response()->json([
            'data' => $classes,
        ]);
    }

    public function students(Request $request, string $classId): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $students = DB::table('class_enrollments')
            ->join('students', 'class_enrollments.student_id', '=', 'students.id')
            ->where('class_enrollments.class_id', $classId)
            ->where('class_enrollments.status', 'active')
            ->where('students.school_id', $schoolId)
            ->select(
                'students.id',
                'students.admission_no',
                'students.nisn',
                'students.name',
                'students.gender',
                'students.status'
            )
            ->orderBy('students.name')
            ->get();

        return response()->json([
            'data' => $students,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $validated = $request->validate([
            'name' => 'required|string|max:50', // e.g. Kelas 2B
            'grade_level' => 'required|string|in:1,2,3,4,5,6',
            'homeroom_membership_id' => 'nullable|uuid',
        ]);

        $academicYear = DB::table('academic_years')
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        $classId = (string) \Illuminate\Support\Str::uuid();
        DB::table('classes')->insert([
            'id' => $classId,
            'school_id' => $schoolId,
            'academic_year_id' => $academicYear?->id ?? (string) \Illuminate\Support\Str::uuid(),
            'homeroom_membership_id' => $validated['homeroom_membership_id'] ?? null,
            'name' => $validated['name'],
            'grade_level' => $validated['grade_level'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Kelas berhasil ditambahkan.',
            'data' => [
                'id' => $classId,
                'name' => $validated['name'],
                'grade_level' => $validated['grade_level'],
            ],
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:50',
            'grade_level' => 'sometimes|required|string|in:1,2,3,4,5,6',
            'homeroom_membership_id' => 'nullable|uuid',
        ]);

        $updateData = array_filter([
            'name' => $validated['name'] ?? null,
            'grade_level' => $validated['grade_level'] ?? null,
            'homeroom_membership_id' => $validated['homeroom_membership_id'] ?? null,
            'updated_at' => now(),
        ]);

        DB::table('classes')
            ->where('school_id', $schoolId)
            ->where('id', $id)
            ->update($updateData);

        return response()->json(['message' => 'Data kelas berhasil diperbarui.']);
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
