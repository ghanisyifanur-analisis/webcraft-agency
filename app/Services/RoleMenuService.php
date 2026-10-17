<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RoleMenuService
{
    /**
     * Get menu list tailored for each role in SD (Sekolah Dasar).
     */
    public static function getMenusForRoles(array $roles): array
    {
        // 1. Super Admin
        if (in_array('super_admin', $roles)) {
            return [
                ['id' => 'access_control', 'label' => 'Kelola Akses', 'icon' => 'admin_panel_settings', 'route' => '/admin/roles', 'color' => '#6366F1'],
                ['id' => 'students', 'label' => 'Data Siswa SD', 'icon' => 'people_alt', 'route' => '/academic', 'color' => '#3B82F6'],
                ['id' => 'spp', 'label' => 'Kelola SPP', 'icon' => 'payments', 'route' => '/finance', 'color' => '#F59E0B'],
                ['id' => 'cards', 'label' => 'Kartu Pelajar', 'icon' => 'badge', 'route' => '/admin/cards', 'color' => '#0D9488'],
                ['id' => 'attendance_scan', 'label' => 'Scan Absensi', 'icon' => 'qr_code_scanner', 'route' => '/scan-qr', 'color' => '#10B981'],
                ['id' => 'reports', 'label' => 'Laporan Rekap', 'icon' => 'analytics', 'route' => '/reports', 'color' => '#8B5CF6'],
                ['id' => 'timetable', 'label' => 'Jadwal & Guru', 'icon' => 'calendar_month', 'route' => '/academic', 'color' => '#EC4899'],
                ['id' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'campaign', 'route' => '/announcements', 'color' => '#0284C7'],
            ];
        }

        // 2. Admin Sekolah
        if (in_array('admin_sekolah', $roles)) {
            return [
                ['id' => 'students', 'label' => 'Data Siswa SD', 'icon' => 'people_alt', 'route' => '/academic', 'color' => '#3B82F6'],
                ['id' => 'spp', 'label' => 'Tagihan SPP SD', 'icon' => 'payments', 'route' => '/finance', 'color' => '#F59E0B'],
                ['id' => 'cards', 'label' => 'Terbitkan Kartu', 'icon' => 'badge', 'route' => '/admin/cards', 'color' => '#0D9488'],
                ['id' => 'classes', 'label' => 'Daftar Kelas SD', 'icon' => 'class', 'route' => '/academic', 'color' => '#8B5CF6'],
                ['id' => 'attendance_scan', 'label' => 'Scan Absen', 'icon' => 'qr_code_scanner', 'route' => '/scan-qr', 'color' => '#10B981'],
                ['id' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'campaign', 'route' => '/announcements', 'color' => '#0284C7'],
            ];
        }

        // 3. Kepala Sekolah
        if (in_array('kepala_sekolah', $roles)) {
            return [
                ['id' => 'reports_attendance', 'label' => 'Rekap Absensi', 'icon' => 'fact_check', 'route' => '/academic', 'color' => '#10B981'],
                ['id' => 'reports_finance', 'label' => 'Laporan SPP SD', 'icon' => 'account_balance', 'route' => '/finance', 'color' => '#F59E0B'],
                ['id' => 'classes_overview', 'label' => 'Pantau Kelas SD', 'icon' => 'visibility', 'route' => '/academic', 'color' => '#3B82F6'],
                ['id' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'campaign', 'route' => '/announcements', 'color' => '#0284C7'],
            ];
        }

        // 4. Wali Kelas (SD)
        if (in_array('wali_kelas', $roles)) {
            return [
                ['id' => 'my_class', 'label' => 'Kelas Binaan 1A', 'icon' => 'groups', 'route' => '/academic', 'color' => '#3B82F6'],
                ['id' => 'attendance_scan', 'label' => 'Scan Absen Kelas', 'icon' => 'qr_code_scanner', 'route' => '/scan-qr', 'color' => '#10B981'],
                ['id' => 'input_timetable', 'label' => 'Jadwal & Guru', 'icon' => 'calendar_month', 'route' => '/academic', 'color' => '#8B5CF6'],
                ['id' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'campaign', 'route' => '/announcements', 'color' => '#0284C7'],
            ];
        }

        // 5. Guru Mapel (PJOK / SBdP / dll)
        if (in_array('guru', $roles)) {
            return [
                ['id' => 'teaching_schedule', 'label' => 'Jadwal Mengajar', 'icon' => 'schedule', 'route' => '/academic', 'color' => '#3B82F6'],
                ['id' => 'attendance_scan', 'label' => 'Scan Absen Mapel', 'icon' => 'qr_code_scanner', 'route' => '/scan-qr', 'color' => '#10B981'],
                ['id' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'campaign', 'route' => '/announcements', 'color' => '#0284C7'],
            ];
        }

        // 6. Orang Tua (Wali Murid - Mendukung Multi Anak / 2 Siswa)
        if (in_array('orang_tua', $roles)) {
            return [
                ['id' => 'children_cards', 'label' => 'Kartu QR Anak', 'icon' => 'qr_code_2', 'route' => '/my-card', 'color' => '#10B981'],
                ['id' => 'children_spp', 'label' => 'Tagihan SPP Anak', 'icon' => 'receipt_long', 'route' => '/finance', 'color' => '#F59E0B'],
                ['id' => 'children_schedule', 'label' => 'Jadwal Belajar', 'icon' => 'calendar_today', 'route' => '/academic', 'color' => '#3B82F6'],
                ['id' => 'children_attendance', 'label' => 'Kehadiran Anak', 'icon' => 'fact_check', 'route' => '/academic', 'color' => '#8B5CF6'],
                ['id' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'campaign', 'route' => '/announcements', 'color' => '#0284C7'],
            ];
        }

        // 7. Murid (Siswa SD)
        if (in_array('murid', $roles)) {
            return [
                ['id' => 'my_card', 'label' => 'Kartu QR Saya', 'icon' => 'qr_code_2', 'route' => '/my-card', 'color' => '#10B981'],
                ['id' => 'my_spp', 'label' => 'Tagihan SPP', 'icon' => 'receipt_long', 'route' => '/finance', 'color' => '#F59E0B'],
                ['id' => 'my_schedule', 'label' => 'Jadwal Belajar', 'icon' => 'calendar_today', 'route' => '/academic', 'color' => '#3B82F6'],
                ['id' => 'my_attendance', 'label' => 'Kehadiran Saya', 'icon' => 'check_circle', 'route' => '/academic', 'color' => '#8B5CF6'],
                ['id' => 'announcements', 'label' => 'Pengumuman', 'icon' => 'campaign', 'route' => '/announcements', 'color' => '#0284C7'],
            ];
        }

        return [];
    }

    /**
     * Get default permissions by role.
     */
    public static function getPermissionsForRoles(array $roles): array
    {
        if (in_array('super_admin', $roles)) {
            return ['all_access', 'manage_access', 'manage_students', 'manage_spp', 'manage_attendance', 'issue_cards', 'view_reports', 'manage_classes'];
        }

        if (in_array('admin_sekolah', $roles)) {
            return ['manage_students', 'manage_spp', 'manage_classes', 'issue_cards', 'scan_attendance', 'publish_announcements'];
        }

        if (in_array('kepala_sekolah', $roles)) {
            return ['view_reports', 'view_attendance_summary', 'view_finance_summary', 'publish_announcements'];
        }

        if (in_array('wali_kelas', $roles)) {
            return ['manage_homeroom', 'assign_teachers', 'scan_attendance', 'view_schedule', 'publish_announcements'];
        }

        if (in_array('guru', $roles)) {
            return ['scan_attendance', 'view_schedule'];
        }

        if (in_array('orang_tua', $roles) || in_array('murid', $roles)) {
            return ['view_own_card', 'view_own_invoices', 'view_schedule'];
        }

        return [];
    }

    /**
     * Get dashboard config (Quick Action, Role Display Name, and Contextual Badges)
     */
    public static function getDashboardConfig(array $roles, $user, ?string $schoolId): array
    {
        // 1. Super Admin
        if (in_array('super_admin', $roles)) {
            return [
                'role_label' => 'Super Administrator',
                'badge' => 'Full Access Controller',
                'quick_action' => [
                    'label' => 'Kelola Aturan Hak Akses & Role',
                    'icon' => 'admin_panel_settings',
                    'route' => '/admin/roles',
                    'button_text' => 'Buka Matriks Akses',
                ],
                'summary_cards' => [
                    ['title' => 'Status Sistem', 'value' => 'Aktif & Normal', 'badge' => 'Online', 'type' => 'success'],
                    ['title' => 'Total Akun Role', 'value' => '7 Akun', 'badge' => 'Terkonfigurasi', 'type' => 'primary'],
                    ['title' => 'Sekolah Aktif', 'value' => 'SD Negeri 1 Teladan', 'badge' => 'SD', 'type' => 'info'],
                ],
            ];
        }

        // 2. Admin Sekolah
        if (in_array('admin_sekolah', $roles)) {
            return [
                'role_label' => 'Admin Sekolah (Tata Usaha)',
                'badge' => 'Tata Usaha & Kesiswaan',
                'quick_action' => [
                    'label' => 'Pindai QR Barcode Siswa',
                    'icon' => 'qr_code_scanner',
                    'route' => '/scan-qr',
                    'button_text' => 'Mulai Scan Presensi',
                ],
                'summary_cards' => [
                    ['title' => 'Data Siswa SD', 'value' => '2 Siswa Aktif', 'badge' => 'Rombel 1A & 3A', 'type' => 'primary'],
                    ['title' => 'Tagihan SPP Okt 2026', 'value' => '2 Tagihan', 'badge' => 'Rp 500.000 Belum Bayar', 'type' => 'warning'],
                ],
            ];
        }

        // 3. Kepala Sekolah
        if (in_array('kepala_sekolah', $roles)) {
            return [
                'role_label' => 'Kepala Sekolah',
                'badge' => 'Monitoring & Kebijakan',
                'quick_action' => [
                    'label' => 'Pantau Rekap Kehadiran Sekolah',
                    'icon' => 'analytics',
                    'route' => '/academic',
                    'button_text' => 'Buka Laporan Kehadiran',
                ],
                'summary_cards' => [
                    ['title' => 'Kehadiran Siswa Hari Ini', 'value' => '98% Hadir', 'badge' => 'Sangat Baik', 'type' => 'success'],
                    ['title' => 'Penerimaan SPP Bulan Ini', 'value' => 'Rp 250.000', 'badge' => '1 Lunas / 2 Belum', 'type' => 'warning'],
                ],
            ];
        }

        // 4. Wali Kelas
        if (in_array('wali_kelas', $roles)) {
            return [
                'role_label' => 'Wali Kelas 1A (SD)',
                'badge' => 'Pembina Kelas 1A',
                'quick_action' => [
                    'label' => 'Scan Presensi QR Siswa Kelas 1A',
                    'icon' => 'qr_code_scanner',
                    'route' => '/scan-qr',
                    'button_text' => 'Scan Presensi Kelas 1A',
                ],
                'summary_cards' => [
                    ['title' => 'Kelas Binaan', 'value' => 'Kelas 1A SD', 'badge' => 'Wali Kelas', 'type' => 'primary'],
                    ['title' => 'Presensi Kelas Hari Ini', 'value' => '100% Hadir', 'badge' => 'Ahmad Fauzan Hadir', 'type' => 'success'],
                    ['title' => 'Guru Mapel Ditugaskan', 'value' => 'PJOK: Rian Hidayat, S.Pd.', 'badge' => 'Jadwal Aktif', 'type' => 'info'],
                ],
            ];
        }

        // 5. Guru Mapel
        if (in_array('guru', $roles)) {
            return [
                'role_label' => 'Guru Mata Pelajaran (PJOK)',
                'badge' => 'Guru Pengajar',
                'quick_action' => [
                    'label' => 'Scan Presensi Jam Mengajar Mapel',
                    'icon' => 'qr_code_scanner',
                    'route' => '/scan-qr',
                    'button_text' => 'Scan Presensi PJOK',
                ],
                'summary_cards' => [
                    ['title' => 'Jadwal Hari Ini', 'value' => 'PJOK di Kelas 1A', 'badge' => '07:30 - 09:00', 'type' => 'primary'],
                    ['title' => 'Penugasan Dari Wali Kelas', 'value' => 'Siti Rahmawati, S.Pd.', 'badge' => 'Kelas 1A', 'type' => 'info'],
                ],
            ];
        }

        // 6. Orang Tua (Wali Murid dengan 2 Anak di 1 Sekolah)
        if (in_array('orang_tua', $roles)) {
            return [
                'role_label' => 'Orang Tua / Wali Murid',
                'badge' => 'Wali dari 2 Siswa SD',
                'quick_action' => [
                    'label' => 'Tampilkan Kartu QR Barcode Anak',
                    'icon' => 'qr_code_2',
                    'route' => '/my-card',
                    'button_text' => 'Buka Kartu QR Barcode',
                ],
                'summary_cards' => [
                    [
                        'title' => 'Anak 1: Ahmad Fauzan (Kelas 1A)',
                        'value' => 'Tagihan SPP Okt: Rp 250.000',
                        'badge' => 'Belum Lunas',
                        'type' => 'warning',
                        'student_id' => 'student-1',
                    ],
                    [
                        'title' => 'Anak 2: Siti Aisyah (Kelas 3A)',
                        'value' => 'Tagihan SPP Okt: Rp 250.000',
                        'badge' => 'Belum Lunas',
                        'type' => 'warning',
                        'student_id' => 'student-2',
                    ],
                ],
            ];
        }

        // 7. Murid (Siswa SD)
        if (in_array('murid', $roles)) {
            return [
                'role_label' => 'Siswa SD Negeri 1 Teladan',
                'badge' => 'Kelas 1A · NISN: 0089123456',
                'quick_action' => [
                    'label' => 'Tampilkan Barcode Kartu Siswa Saya',
                    'icon' => 'qr_code_2',
                    'route' => '/my-card',
                    'button_text' => 'Buka Barcode Kartu Saya',
                ],
                'summary_cards' => [
                    ['title' => 'Kartu Barcode Siswa', 'value' => 'KP-SD-2026-0001', 'badge' => 'Siap Di-Scan', 'type' => 'success'],
                    ['title' => 'Tagihan SPP Okt 2026', 'value' => 'Rp 250.000', 'badge' => 'Belum Lunas', 'type' => 'warning'],
                    ['title' => 'Status Kehadiran', 'value' => 'Hadir Pagi Ini', 'badge' => 'Tercatat', 'type' => 'info'],
                ],
            ];
        }

        return [
            'role_label' => 'Pengguna',
            'badge' => 'Warga Sekolah',
            'quick_action' => [
                'label' => 'Buka Menu',
                'icon' => 'apps',
                'route' => '/dashboard',
                'button_text' => 'Lihat Layanan',
            ],
            'summary_cards' => [],
        ];
    }
}

