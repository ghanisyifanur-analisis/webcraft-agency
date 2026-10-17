<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId($request);
        $user = $request->user();

        // Get user roles
        $userRoles = DB::table('school_memberships')
            ->join('membership_roles', 'school_memberships.id', '=', 'membership_roles.membership_id')
            ->join('roles', 'membership_roles.role_id', '=', 'roles.id')
            ->where('school_memberships.user_id', $user->id)
            ->pluck('roles.name')
            ->toArray();

        $query = DB::table('announcements')
            ->leftJoin('announcement_reads', function ($join) use ($user) {
                $join->on('announcements.id', '=', 'announcement_reads.announcement_id')
                    ->where('announcement_reads.user_id', $user->id);
            })
            ->leftJoin('users as creator', 'announcements.created_by', '=', 'creator.id')
            ->where('announcements.school_id', $schoolId)
            ->where('announcements.status', 'published')
            ->select(
                'announcements.id',
                'announcements.title',
                'announcements.body',
                'announcements.published_at',
                'creator.name as author_name',
                DB::raw('case when announcement_reads.read_at is not null then true else false end as is_read')
            );

        // If not super_admin or kepala_sekolah, filter by audience
        $canViewAll = in_array('super_admin', $userRoles) || in_array('kepala_sekolah', $userRoles);
        if (! $canViewAll) {
            $allowedAudiences = array_merge(['all'], $userRoles);
            $query->where(function ($q) use ($allowedAudiences) {
                $q->whereExists(function ($sub) use ($allowedAudiences) {
                    $sub->select(DB::raw(1))
                        ->from('announcement_audiences')
                        ->whereColumn('announcement_audiences.announcement_id', 'announcements.id')
                        ->whereIn('announcement_audiences.audience_type', $allowedAudiences);
                })->orWhereNotExists(function ($sub) {
                    // Announcements without explicit audience are open to all
                    $sub->select(DB::raw(1))
                        ->from('announcement_audiences')
                        ->whereColumn('announcement_audiences.announcement_id', 'announcements.id');
                });
            });
        }

        $announcements = $query->orderByDesc('announcements.published_at')->paginate(20);

        // Attach audiences to each announcement item
        $items = $announcements->items();
        foreach ($items as $item) {
            $audiences = DB::table('announcement_audiences')
                ->where('announcement_id', $item->id)
                ->pluck('audience_type')
                ->toArray();
            $item->target_roles = empty($audiences) ? ['all'] : $audiences;
        }

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $announcements->currentPage(),
                'total' => $announcements->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $schoolId = $this->getSchoolId($request);

        // Check permission (Kepala Sekolah, Admin, Super Admin)
        $userRoles = DB::table('school_memberships')
            ->join('membership_roles', 'school_memberships.id', '=', 'membership_roles.membership_id')
            ->join('roles', 'membership_roles.role_id', '=', 'roles.id')
            ->where('school_memberships.user_id', $user->id)
            ->pluck('roles.name')
            ->toArray();

        $canCreate = in_array('kepala_sekolah', $userRoles) ||
                     in_array('super_admin', $userRoles) ||
                     in_array('admin_sekolah', $userRoles);

        if (! $canCreate) {
            return response()->json(['message' => 'Hanya Kepala Sekolah atau Administrator yang dapat menyampaikan pengumuman.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'target_roles' => 'required|array|min:1', // ['admin_sekolah', 'wali_kelas', 'guru', 'murid', 'orang_tua', 'all']
            'target_roles.*' => 'string|in:all,admin_sekolah,wali_kelas,guru,murid,orang_tua',
        ]);

        $announcementId = (string) Str::uuid();
        DB::table('announcements')->insert([
            'id' => $announcementId,
            'school_id' => $schoolId,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'published_at' => now(),
            'created_by' => $user->id,
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert target audiences
        foreach ($validated['target_roles'] as $targetRole) {
            DB::table('announcement_audiences')->insert([
                'id' => (string) Str::uuid(),
                'announcement_id' => $announcementId,
                'audience_type' => $targetRole,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Pengumuman berhasil disampaikan ke peran terpilih.',
            'data' => [
                'id' => $announcementId,
                'title' => $validated['title'],
                'target_roles' => $validated['target_roles'],
            ],
        ], 201);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        DB::table('announcement_reads')->updateOrInsert(
            ['announcement_id' => $id, 'user_id' => $user->id],
            ['read_at' => now()]
        );

        return response()->json(['message' => 'Pengumuman telah ditandai dibaca.']);
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
