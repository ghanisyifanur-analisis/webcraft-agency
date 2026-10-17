<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;
use App\Models\ServicePackage;
use App\Models\Order;
use App\Models\OrderAddon;
use App\Models\BriefSubmission;
use App\Models\ProjectMilestone;
use App\Models\Invoice;
use App\Models\CredentialVault;
use App\Models\FinancialLog;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed 5 Roles Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@webcraft.id'],
            [
                'name' => 'MaoneArt Super Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone_number' => '081288880001',
                'status' => 'active',
            ]
        );

        $pm = User::firstOrCreate(
            ['email' => 'pm@webcraft.id'],
            [
                'name' => 'Budi Santoso (Lead PM)',
                'password' => Hash::make('pm123456'),
                'role' => 'pm',
                'phone_number' => '081288880002',
                'status' => 'active',
            ]
        );

        $dev = User::firstOrCreate(
            ['email' => 'dev@webcraft.id'],
            [
                'name' => 'Rian Hidayat (Fullstack Dev)',
                'password' => Hash::make('dev123456'),
                'role' => 'developer',
                'phone_number' => '081288880003',
                'status' => 'active',
            ]
        );

        $finance = User::firstOrCreate(
            ['email' => 'finance@webcraft.id'],
            [
                'name' => 'Siti Rahma (Finance Officer)',
                'password' => Hash::make('finance123'),
                'role' => 'finance',
                'phone_number' => '081288880004',
                'status' => 'active',
            ]
        );

        $clientUser = User::firstOrCreate(
            ['email' => 'klien@webcraft.id'],
            [
                'name' => 'Hendra Pratama',
                'password' => Hash::make('klien123'),
                'role' => 'client',
                'phone_number' => '081234567890',
                'status' => 'active',
            ]
        );

        // 2. Client Profile
        $client = Client::firstOrCreate(
            ['user_id' => $clientUser->id],
            [
                'company_name' => 'PT Kopi Nusantara Raya',
                'business_sector' => 'F&B & Retail Kafe',
                'whatsapp_no' => '081234567890',
                'address' => 'Jl. Senopati No. 42, Kebayoran Baru, Jakarta Selatan',
            ]
        );

        // 3. Service Packages
        $pkg1 = ServicePackage::firstOrCreate(
            ['slug' => 'landing-page-umkm'],
            [
                'name' => 'Landing Page UMKM',
                'base_price' => 750000,
                'features_list' => [
                    '1 Halaman Beranda Interaktif',
                    'Desain Modern & Mobile-Friendly',
                    'Formulir Kontak Terhubung ke WhatsApp',
                    'Gratis Domain .com (Tahun Pertama)',
                    'Hosting NVMe SSD Berkecepatan Tinggi',
                    'Setup Google Search Console & SEO Dasar',
                ],
                'revision_quota' => 2,
                'estimated_days' => 3,
                'icon' => 'rocket-outline',
                'badge' => 'HEMAT',
                'is_featured' => false,
            ]
        );

        $pkg2 = ServicePackage::firstOrCreate(
            ['slug' => 'company-profile-pro'],
            [
                'name' => 'Company Profile Pro',
                'base_price' => 1500000,
                'features_list' => [
                    'Hingga 5 Halaman Dinamis (Home, About, Services, Blog, Contact)',
                    'CMS Admin Panel Mandiri untuk Update Konten',
                    'Desain Eksklusif Brand Identity Kustom',
                    'Integrasi Google Maps & Live Chat',
                    'Email Bisnis Profesional (@namaperusahaan.com)',
                    'Optimasi PageSpeed Skor 90+',
                ],
                'revision_quota' => 3,
                'estimated_days' => 7,
                'icon' => 'business-outline',
                'badge' => 'POPULER',
                'is_featured' => true,
            ]
        );

        $pkg3 = ServicePackage::firstOrCreate(
            ['slug' => 'e-commerce-store'],
            [
                'name' => 'E-Commerce Store',
                'base_price' => 2500000,
                'features_list' => [
                    'Katalog Produk Tanpa Batas & Keranjang Belanja',
                    'Integrasi Payment Gateway Otomatis (VA, QRIS, E-Wallet)',
                    'Kalkulator Ongkos Kirim Otomatis (JNE, J&T, SiCepat)',
                    'Manajemen Stok & Laporan Penjualan',
                    'Notifikasi Pesanan Otomatis via WhatsApp',
                    'Kupon Diskon & Flash Sale Engine',
                ],
                'revision_quota' => 4,
                'estimated_days' => 14,
                'icon' => 'cart-outline',
                'badge' => 'TERBAIK',
                'is_featured' => false,
            ]
        );

        $pkg4 = ServicePackage::firstOrCreate(
            ['slug' => 'custom-web-application'],
            [
                'name' => 'Custom Web Application',
                'base_price' => 5000000,
                'features_list' => [
                    'Arsitektur Backend Kustom (Laravel / Node.js API)',
                    'Multi-Role User Dashboard & Role-Based Access Control',
                    'Integrasi Sistem Pihak Ketiga & Webhook Handler',
                    'Database Teroptimasi Tinggi (PostgreSQL / MariaDB)',
                    'Full Containerized Docker Deployment',
                    'Garansi Pemeliharaan & SLA Bug-Fix 90 Hari',
                ],
                'revision_quota' => 5,
                'estimated_days' => 21,
                'icon' => 'code-slash-outline',
                'badge' => 'ENTERPRISE',
                'is_featured' => false,
            ]
        );

        // 4. Sample Order 1 (In Progress with Termin 1 Paid)
        $order = Order::firstOrCreate(
            ['order_number' => 'ORD-20261014-0012'],
            [
                'client_id' => $client->id,
                'package_id' => $pkg2->id,
                'assigned_pm_id' => $pm->id,
                'assigned_dev_id' => $dev->id,
                'total_amount' => 1800000, // 1.5M base + 300k addon
                'status' => 'IN_PROGRESS',
                'start_date' => '2026-10-14',
                'target_completion_date' => '2026-10-21',
                'revisions_used' => 1,
                'admin_notes' => 'Klien meminta warna dominan Dark Emerald & Gold. Desain staging ditargetkan tayang tgl 18 Oktober.',
            ]
        );

        // Addon
        OrderAddon::firstOrCreate(
            ['order_id' => $order->id, 'addon_name' => 'Pembuatan Konten Copywriting 5 Halaman'],
            [
                'price' => 300000,
                'additional_days' => 2,
            ]
        );

        // Brief Submission
        BriefSubmission::firstOrCreate(
            ['order_id' => $order->id],
            [
                'brand_name' => 'Kopi Nusantara Raya',
                'tagline_description' => 'Menyajikan Cita Rasa Biji Kopi Asli Petani Indonesia dengan Roasting Presisi.',
                'color_palette' => '#064e3b (Forest Green) & #d97706 (Amber Gold)',
                'target_audience' => 'Penikmat kopi usia 20-45 tahun, eksekutif muda, dan pelaku usaha kafe modern.',
                'competitor_references' => 'https://tanameracoffee.com, https://fore.coffee',
                'logo_file_path' => 'logo-kopi-nusantara.png',
                'supporting_assets_url' => 'https://drive.google.com/drive/folders/sample-kopi-nusantara',
                'feature_notes' => 'Perlu menu katalog biji kopi dengan tombol pesan langsung ke WhatsApp barista.',
                'status' => 'ACCEPTED',
                'pm_feedback' => 'Brief lengkap dan aset foto beresolusi tinggi sudah diverifikasi siap dieksekusi.',
                'reviewed_at' => now()->subDays(2),
            ]
        );

        // 5 Project Milestones
        $m1 = ProjectMilestone::firstOrCreate(
            ['order_id' => $order->id, 'stage_number' => 1],
            [
                'title' => 'Fase 1: Review Brief & Konsep Sitemap',
                'description' => 'Menganalisis kebutuhan bisnis, memetakan sitemap halaman, dan merumuskan arsitektur informasi website.',
                'staging_url' => null,
                'status' => 'APPROVED',
                'approved_at' => now()->subDays(2),
                'approved_ip' => '114.122.34.88',
            ]
        );

        $m2 = ProjectMilestone::firstOrCreate(
            ['order_id' => $order->id, 'stage_number' => 2],
            [
                'title' => 'Fase 2: Desain UI/UX & Wireframing',
                'description' => 'Penyusunan prototype interaktif Figma, pemilihan palet warna, tipografi, dan layout responsif.',
                'staging_url' => 'https://figma.com/@sample-webcraft/kopi-nusantara',
                'status' => 'APPROVED',
                'approved_at' => now()->subDay(),
                'approved_ip' => '114.122.34.88',
            ]
        );

        $m3 = ProjectMilestone::firstOrCreate(
            ['order_id' => $order->id, 'stage_number' => 3],
            [
                'title' => 'Fase 3: Slicing Kode & Pengembangan Sistem',
                'description' => 'Coding frontend Tailwind CSS, integrasi backend Laravel, dan setup database PostgreSQL.',
                'staging_url' => null,
                'status' => 'ACTIVE',
            ]
        );

        $m4 = ProjectMilestone::firstOrCreate(
            ['order_id' => $order->id, 'stage_number' => 4],
            [
                'title' => 'Fase 4: Pengujian di Server Staging & Evaluasi Klien',
                'description' => 'Pementasan website di server staging demo untuk ditinjau langsung oleh klien sebelum pelunasan.',
                'staging_url' => 'https://staging.maoneart.my.id/demo/kopi-nusantara',
                'status' => 'LOCKED',
            ]
        );

        $m5 = ProjectMilestone::firstOrCreate(
            ['order_id' => $order->id, 'stage_number' => 5],
            [
                'title' => 'Fase 5: Pelunasan, Migrasi ke Server Live, & Final Handover',
                'description' => 'Pelunasan invoice Termin 2, migrasi ke domain utama, penyerahan password vault, dan penerbitan BAST resmi.',
                'staging_url' => null,
                'status' => 'LOCKED',
            ]
        );

        // Invoices: Termin 1 (PAID) and Termin 2 (UNPAID)
        Invoice::firstOrCreate(
            ['invoice_number' => 'INV-202610-0012-DP'],
            [
                'order_id' => $order->id,
                'term_type' => 'TERM_1_DP',
                'amount' => 900000, // 50% of 1.8M
                'due_date' => '2026-10-15',
                'status' => 'PAID',
                'paid_at' => '2026-10-14 14:20:00',
            ]
        );

        Invoice::firstOrCreate(
            ['invoice_number' => 'INV-202610-0012-PELUNASAN'],
            [
                'order_id' => $order->id,
                'term_type' => 'TERM_2_SETTLEMENT',
                'amount' => 900000, // 50% of 1.8M
                'due_date' => '2026-10-21',
                'status' => 'UNPAID',
            ]
        );

        // Credential Vault (Encrypted & Locked)
        CredentialVault::firstOrCreate(
            ['order_id' => $order->id, 'service_label' => 'cPanel & Hosting Server'],
            [
                'login_url' => 'https://cpanel.kopinusantara.com:2083',
                'username' => 'kopinusantara_host',
                'plain_password' => 'K0p1Nus4nt4r4#2026!Secure',
                'is_unlocked' => false, // Will unlock automatically when Termin 2 is PAID
            ]
        );

        CredentialVault::firstOrCreate(
            ['order_id' => $order->id, 'service_label' => 'Admin CMS Dashboard'],
            [
                'login_url' => 'https://kopinusantara.com/admin/login',
                'username' => 'admin_hendra',
                'plain_password' => 'B4r1st4P4ssw0rd!2026',
                'is_unlocked' => false,
            ]
        );

        // Financial COGS Log
        FinancialLog::firstOrCreate(
            ['order_id' => $order->id, 'expense_title' => 'Registrasi Domain kopinusantara.com'],
            [
                'expense_amount' => 165000,
                'category' => 'domain',
                'vendor_name' => 'JagoanHosting Registrar',
                'expense_date' => '2026-10-14',
                'notes' => 'Sewa domain 1 tahun',
            ]
        );

        FinancialLog::firstOrCreate(
            ['order_id' => $order->id, 'expense_title' => 'Alokasi Hosting Cloud NVMe 5GB'],
            [
                'expense_amount' => 250000,
                'category' => 'hosting',
                'vendor_name' => 'JagoanHosting Server',
                'expense_date' => '2026-10-14',
                'notes' => 'Alokasi resource 1 tahun',
            ]
        );
    }
}
