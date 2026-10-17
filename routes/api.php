<?php

use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClassController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FinanceController;
use App\Http\Controllers\Api\V1\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - V1
|--------------------------------------------------------------------------
| Prefix: /api/v1 (configured in bootstrap/app.php)
| Source of Truth for School Management System
*/

// Service Health / Status
Route::get('/', function () {
    return response()->json([
        'status' => 'online',
        'service' => 'API Manajemen Sekolah',
        'version' => '1.0.0',
        'time' => now()->toIso8601String(),
    ]);
});

// Public Authentication
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

// Protected routes (Requires Sanctum personal access token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Identity
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/schools', [AuthController::class, 'schools']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Master Data: Students & Student Cards (Kartu Pelajar)
    Route::get('/my-card', [StudentController::class, 'myCard']);
    Route::get('/students', [StudentController::class, 'index']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::get('/students/{student}', [StudentController::class, 'show']);
    Route::put('/students/{student}', [StudentController::class, 'update']);
    Route::get('/students/{student}/card/print', [StudentController::class, 'printCard']);
    Route::post('/students/{student}/cards/issue', [StudentController::class, 'issueCard']);
    Route::post('/student-cards/{card}/revoke', [StudentController::class, 'revokeCard']);

    // Classes & Timetable Assignments (Wali Kelas)
    Route::get('/classes', [ClassController::class, 'index']);
    Route::post('/classes', [ClassController::class, 'store']);
    Route::put('/classes/{class}', [ClassController::class, 'update']);
    Route::get('/classes/{class}/students', [ClassController::class, 'students']);
    Route::get('/my-class', function (Illuminate\Http\Request $request) {
        $user = $request->user();
        $membership = DB::table('school_memberships')->where('user_id', $user->id)->first();
        $homeroomClass = DB::table('classes')->where('homeroom_membership_id', $membership?->id)->first();
        
        $assignments = [];
        if ($homeroomClass) {
            $assignments = DB::table('teaching_assignments')
                ->join('subjects', 'teaching_assignments.subject_id', '=', 'subjects.id')
                ->join('school_memberships', 'teaching_assignments.teacher_membership_id', '=', 'school_memberships.id')
                ->join('users', 'school_memberships.user_id', '=', 'users.id')
                ->where('teaching_assignments.class_id', $homeroomClass->id)
                ->select('teaching_assignments.id', 'subjects.name as subject_name', 'subjects.code as subject_code', 'users.name as teacher_name')
                ->get();
        }

        return response()->json([
            'data' => [
                'class' => $homeroomClass,
                'assignments' => $assignments,
            ],
        ]);
    });

    // Super Admin: Role & Access Rules Management
    Route::get('/roles', function (Illuminate\Http\Request $request) {
        $roles = DB::table('roles')->get();
        $permissions = DB::table('permissions')->get();
        $rolePermissions = DB::table('role_permissions')->get();

        return response()->json([
            'data' => [
                'roles' => $roles,
                'permissions' => $permissions,
                'role_permissions' => $rolePermissions,
            ],
        ]);
    });

    Route::put('/roles/{id}/permissions', function (Illuminate\Http\Request $request, string $id) {
        $validated = $request->validate([
            'permission_ids' => 'required|array',
        ]);

        DB::table('role_permissions')->where('role_id', $id)->delete();
        foreach ($validated['permission_ids'] as $pId) {
            DB::table('role_permissions')->insert([
                'role_id' => $id,
                'permission_id' => $pId,
            ]);
        }

        return response()->json(['message' => 'Hak akses peran berhasil diperbarui.']);
    });

    // Attendance & QR Scanner
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance/scans', [AttendanceController::class, 'scan']);
    Route::get('/attendance/sessions/{session}/records', [AttendanceController::class, 'sessionRecords']);

    // Finance & Invoices
    Route::get('/invoices', [FinanceController::class, 'invoices']);
    Route::get('/invoices/{invoice}', [FinanceController::class, 'show']);
    Route::post('/invoices/{invoice}/payments', [FinanceController::class, 'recordPayment']);

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::post('/announcements/{id}/read', [AnnouncementController::class, 'markAsRead']);
});

