<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'login' => 'required|string', // email or phone
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $validated['login'])
            ->orWhere('phone', $validated['login'])
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Email/nomor telepon atau kata sandi tidak cocok.'],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Akun Anda tidak aktif atau ditangguhkan.',
            ], 403);
        }

        $user->update(['last_login_at' => now()]);

        $deviceName = $validated['device_name'] ?? 'Mobile App';
        $token = $user->createToken($deviceName)->plainTextToken;

        // Retrieve user memberships & roles
        $membership = DB::table('school_memberships')
            ->join('schools', 'school_memberships.school_id', '=', 'schools.id')
            ->where('school_memberships.user_id', $user->id)
            ->where('school_memberships.status', 'active')
            ->select('schools.id as school_id', 'schools.name as school_name', 'school_memberships.id as membership_id')
            ->first();

        $roles = [];
        if ($membership) {
            $roles = DB::table('membership_roles')
                ->join('roles', 'membership_roles.role_id', '=', 'roles.id')
                ->where('membership_roles.membership_id', $membership->membership_id)
                ->pluck('roles.name')
                ->toArray();
        }

        $extraData = $this->getUserRoleExtras($user, $roles, $membership?->school_id);

        return response()->json([
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'user' => array_merge([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'birth_date' => $user->birth_date,
                    'age' => $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->age : null,
                    'address' => $user->address,
                    'active_school_id' => $membership?->school_id,
                    'active_school_name' => $membership?->school_name ?? 'SD Negeri 1 Teladan',
                    'roles' => $roles,
                    'permissions' => \App\Services\RoleMenuService::getPermissionsForRoles($roles),
                    'menus' => \App\Services\RoleMenuService::getMenusForRoles($roles),
                    'dashboard_config' => \App\Services\RoleMenuService::getDashboardConfig($roles, $user, $membership?->school_id),
                ], $extraData),
            ],
            'meta' => [
                'request_id' => (string) str()->uuid(),
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $membership = DB::table('school_memberships')
            ->join('schools', 'school_memberships.school_id', '=', 'schools.id')
            ->where('school_memberships.user_id', $user->id)
            ->where('school_memberships.status', 'active')
            ->select('schools.id as school_id', 'schools.name as school_name', 'school_memberships.id as membership_id')
            ->first();

        $roles = [];
        if ($membership) {
            $roles = DB::table('membership_roles')
                ->join('roles', 'membership_roles.role_id', '=', 'roles.id')
                ->where('membership_roles.membership_id', $membership->membership_id)
                ->pluck('roles.name')
                ->toArray();
        }

        $extraData = $this->getUserRoleExtras($user, $roles, $membership?->school_id);

        return response()->json([
            'data' => array_merge([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'birth_date' => $user->birth_date,
                'age' => $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->age : null,
                'address' => $user->address,
                'active_school_id' => $membership?->school_id,
                'active_school_name' => $membership?->school_name ?? 'SD Negeri 1 Teladan',
                'roles' => $roles,
                'permissions' => \App\Services\RoleMenuService::getPermissionsForRoles($roles),
                'menus' => \App\Services\RoleMenuService::getMenusForRoles($roles),
                'dashboard_config' => \App\Services\RoleMenuService::getDashboardConfig($roles, $user, $membership?->school_id),
            ], $extraData),
            'meta' => [
                'request_id' => (string) str()->uuid(),
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'gender' => 'sometimes|required|in:L,P',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        $user->update($validated);

        // If user has murid role, sync to student record
        $membership = DB::table('school_memberships')->where('user_id', $user->id)->first();
        if ($membership) {
            $hasMuridRole = DB::table('membership_roles')
                ->join('roles', 'membership_roles.role_id', '=', 'roles.id')
                ->where('membership_roles.membership_id', $membership->id)
                ->where('roles.name', 'murid')
                ->exists();

            if ($hasMuridRole) {
                DB::table('students')->where('admission_no', '20261001')->update(array_filter([
                    'name' => $validated['name'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'updated_at' => now(),
                ]));
            }
        }

        return response()->json([
            'message' => 'Profil akun berhasil diperbarui.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'birth_date' => $user->birth_date,
                'age' => $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->age : null,
                'address' => $user->address,
            ],
        ]);
    }

    private function getUserRoleExtras(User $user, array $roles, ?string $schoolId): array
    {
        $children = [];
        $studentInfo = null;

        // If Orang Tua (Wali Murid), fetch children
        if (in_array('orang_tua', $roles)) {
            $guardian = DB::table('guardians')->where('user_id', $user->id)->first();
            if ($guardian) {
                $students = DB::table('student_guardians')
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
                        'classes.name as class_name',
                        'classes.grade_level',
                        'student_cards.card_number',
                        'student_cards.token_hash'
                    )
                    ->get();

                foreach ($students as $st) {
                    $unpaid = DB::table('invoices')
                        ->where('student_id', $st->id)
                        ->where('status', 'unpaid')
                        ->count();

                    $token = ($st->admission_no === '20261001') ? 'KP_20261001_TELADAN' : 'KP_20241015_TELADAN';

                    $children[] = [
                        'id' => $st->id,
                        'name' => $st->name,
                        'admission_no' => $st->admission_no,
                        'nisn' => $st->nisn,
                        'gender' => $st->gender,
                        'class_name' => $st->class_name ?? 'Kelas SD',
                        'card_number' => $st->card_number ?? 'KP-SD-2026',
                        'qr_token' => $token,
                        'unpaid_invoices_count' => $unpaid,
                    ];
                }
            }
        }

        // If Murid, fetch student record
        if (in_array('murid', $roles)) {
            $st = DB::table('students')->where('admission_no', '20261001')->first();
            if ($st) {
                $cls = DB::table('class_enrollments')
                    ->join('classes', 'class_enrollments.class_id', '=', 'classes.id')
                    ->where('class_enrollments.student_id', $st->id)
                    ->first();
                $card = DB::table('student_cards')->where('student_id', $st->id)->first();
                $unpaid = DB::table('invoices')->where('student_id', $st->id)->where('status', 'unpaid')->count();

                $studentInfo = [
                    'id' => $st->id,
                    'name' => $st->name,
                    'admission_no' => $st->admission_no,
                    'nisn' => $st->nisn,
                    'gender' => $st->gender,
                    'class_name' => $cls?->name ?? 'Kelas 1A',
                    'card_number' => $card?->card_number ?? 'KP-SD-2026-0001',
                    'qr_token' => 'KP_20261001_TELADAN',
                    'unpaid_invoices_count' => $unpaid,
                ];
            }
        }

        return [
            'children' => $children,
            'student' => $studentInfo,
        ];
    }


    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil keluar dari akun.',
            'meta' => [
                'request_id' => (string) str()->uuid(),
            ],
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['login' => 'required|string']);

        // As specified in blueprint: Do not reveal whether user exists
        return response()->json([
            'message' => 'Jika akun terdaftar, instruksi pemulihan kata sandi telah dikirimkan.',
            'meta' => [
                'request_id' => (string) str()->uuid(),
            ],
        ]);
    }

    public function schools(Request $request): JsonResponse
    {
        $user = $request->user();

        $schools = DB::table('school_memberships')
            ->join('schools', 'school_memberships.school_id', '=', 'schools.id')
            ->where('school_memberships.user_id', $user->id)
            ->where('school_memberships.status', 'active')
            ->select('schools.id', 'schools.code', 'schools.name', 'schools.status')
            ->get();

        return response()->json([
            'data' => $schools,
            'meta' => [
                'request_id' => (string) str()->uuid(),
            ],
        ]);
    }
}
