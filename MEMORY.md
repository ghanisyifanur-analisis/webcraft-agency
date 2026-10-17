# Persistent Memory - WebCraft Agency Platform

## 1. Project Context & Purpose
- **Nama Proyek**: WebCraft Agency Platform
- **Spesifikasi**: Standar PRD & SRS ISO/IEC/IEEE 29148:2018 (Tugas Besar Web Programming Semester 5).
- **Core Stacks**: Laravel 11/13, PostgreSQL 15, Docker & Docker Compose, Tailwind CSS, IonIcons, AES-256 Gated Storage.
- **Dual Deliverable Architecture**:
  1. Standalone Repo (`/sdcard/www/webcraft-agency`): Lengkap dengan `Dockerfile`, `docker-compose.yml`, PostgreSQL migrations, models, controllers, dan seeder untuk pengumpulan ke dosen/penguji.
  2. Live Demo Bridge (`/sdcard/www/maoneart.my.id/public/webcraft-agency/` & `projek1/`): Dapat diakses live publik di `https://maoneart.my.id/webcraft-agency/` dan `https://maoneart.my.id/projek1/` tanpa hambatan keterbatasan Docker/PostgreSQL pada shared hosting.

## 2. Recent Milestones & Key Changes
- **Database & Architecture**:
  - Tabel migrasi: `users` (5 role), `clients`, `service_packages`, `orders`, `order_addons`, `brief_submissions`, `project_milestones`, `revision_tickets`, `invoices`, `payments`, `credential_vaults`, `financial_logs`.
  - Enkripsi AES-256 pada model `CredentialVault` dengan gated conditional release.
  - Seeder bawaan: 5 akun default, 4 paket layanan, 1 order demo lengkap dengan 5 milestone, split billing, dan aset vault.
- **Controllers & Routing**:
  - `HomeController`, `AuthController`, `OrderController`, `BriefController`, `MilestoneController`, `RevisionController`, `PaymentController`, `VaultController`, `DashboardController`.
- **Blade Views (Glassmorphism & MaoneArt Modal Standard)**:
  - `layouts/app.blade.php`: Modal konfirmasi & alert simetris 2-kolom (`grid grid-cols-2 gap-3 w-full`).
  - `home.blade.php`: Realtime dynamic quotation calculator (FR-EST-01 & FR-EST-02).
  - `auth/login.blade.php`: Login biasa & 1-Click test role quick switcher.
  - `orders/checkout.blade.php` & `orders/show.blade.php`: 5-milestone pipeline & tracking.
  - `briefs/create.blade.php`: Onboarding brief klien.
  - `invoices/show.blade.php`: Split billing & Midtrans multi-channel simulator.
  - `vault/bast.blade.php`: Cetak Berita Acara Serah Terima digital resmi.
  - 5 Dashboard Views: Super Admin, PM, Developer, Finance, Client Portal.
- **Deployment & Hosting Integration**:
  - Folder `public/projek1/` dan `public/webcraft-agency.zip` di-deploy ke `maoneart.my.id`.
  - Pushed to `origin/main` (GitHub) dan `hosting/main` (JagoanHosting).
  - HTTP/2 200 OK live di `https://maoneart.my.id/projek1/`.

## 3. Architecture & Technical Decisions
- **Shared Hosting vs Docker/PostgreSQL**: Shared hosting (cPanel/JagoanHosting) tidak mendukung daemon Docker dan PostgreSQL native. Solusi: Demo interaktif live diletakkan di `public/projek1/` yang kompatibel dengan PHP/LiteSpeed hosting, sedangkan package source code Docker + PostgreSQL disediakan untuk diunduh langsung (`/webcraft-agency.zip`) atau dijalankan dosen secara lokal.
- **Modal System**: Sesuai Rule 5, 100% menggunakan modal kustom MaoneArt Glassmorphism, dilarang memakai `confirm()` / `alert()` native browser.

## 4. Accounts & Credentials Matrix
- Super Admin: `admin@webcraft.id` / `admin123`
- Project Manager: `pm@webcraft.id` / `pm123456`
- Developer: `dev@webcraft.id` / `dev123456`
- Finance: `finance@webcraft.id` / `finance123`
- Klien Portal: `klien@webcraft.id` / `klien123`
