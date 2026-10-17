# WebCraft Agency Platform (Academic Production Engine)

Platform agensi pembuatan website dan aplikasi berbasis web profesional dengan standar kepatuhan **ISO/IEC/IEEE 29148:2018 (Requirements Engineering)** untuk Ujian Akhir Semester / Tugas Besar Web Programming (Semester 5).

---

## 🌐 Demo Interaktif Live
- **URL Demo**: [https://maoneart.my.id/projek1/](https://maoneart.my.id/projek1/)
- **Unduh Source Code (.zip)**: [https://maoneart.my.id/webcraft-agency.zip](https://maoneart.my.id/webcraft-agency.zip)

---

## 🛠️ Tech Stack & Spesifikasi Teknis
- **Backend Framework**: Laravel 11 / 13 (PHP 8.2+)
- **Database Utama**: PostgreSQL 15 (Docker Production) & SQLite / MariaDB (Local / Testing)
- **Containerization**: Docker & Docker Compose multi-service (`laravel_app` & `postgres_db`)
- **Frontend & Styling**: Tailwind CSS, IonIcons 7, MaoneArt Glassmorphism Dark Theme
- **Enkripsi Kredensial**: AES-256-CBC (Gated Access Protocol)
- **Payment Gateway**: Midtrans Snap / Webhook Idempotency Simulation

---

## 👥 5 Peran Pengguna (Multi-Role Access Control)
| Role | Email Demo | Password | Ruang Lingkup & Tanggung Jawab |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@webcraft.id` | `admin123` | Overview omzet, alokasi PM & Developer, pantau COGS modal |
| **Project Manager (PM)** | `pm@webcraft.id` | `pm123456` | Review brief klien, verifikasi scope revisi, sprint roadmap |
| **Developer / Lead** | `dev@webcraft.id` | `dev123456` | Pengerjaan fitur, publikasi link server staging, input vault AES-256 |
| **Finance** | `finance@webcraft.id` | `finance123` | Pantau arus kas masuk, invoice split billing, pencatatan beban modal riil (COGS) |
| **Klien Portal** | `klien@webcraft.id` | `klien123` | Isi brief kebutuhan, digital sign-off 5 milestone, buka brankas, cetak BAST |

---

## 📋 5 Milestone Alur Kerja (Workflow Pipeline)
1. **Fase 1: Review Brief & Konsep Sitemap**  
   Pengumpulan identitas brand, preferensi warna, aset logo, dan pemetaan halaman setelah DP 50% terverifikasi.
2. **Fase 2: Desain UI/UX & Wireframing**  
   Perancangan visual layout responsif di Figma, tipografi, dan sign-off digital desain.
3. **Fase 3: Slicing Kode & Pengembangan Sistem**  
   Implementasi frontend Tailwind CSS dan logika backend database (Laravel MVC).
4. **Fase 4: Pengujian di Server Staging & Evaluasi Klien**  
   Pementasan website pada subdomain staging untuk live review, pengetesan fitur, dan pengajuan tiket revisi (2x gratis vs add-on Rp 200.000).
5. **Fase 5: Pelunasan, Handover Kredensial, & BAST Resmi**  
   Pelunasan Termin 2 (50%), auto-unlock Brankas Kredensial terenkripsi AES-256, dan penerbitan Berita Acara Serah Terima (BAST).

---

## 🚀 Cara Menjalankan dengan Docker (Rekomendasi Dosen / Penguji)

### 1. Prasyarat
- Docker Engine & Docker Compose terpasang pada komputer / laptop penguji.

### 2. Jalankan Kontainer
```bash
# Clone atau ekstrak direktori webcraft-agency
cd webcraft-agency

# Salin konfigurasi environment
cp .env.example .env

# Build dan jalankan seluruh container (Laravel + PostgreSQL 15)
docker compose up -d --build

# Generate App Key
docker compose exec laravel_app php artisan key:generate

# Jalankan migrasi dan seeder database
docker compose exec laravel_app php artisan migrate:fresh --seed
```

### 3. Akses Aplikasi
- Buka browser: `http://localhost:8000`
- Gunakan salah satu kredensial akun di atas atau klik tombol 1-Click Quick Login pada halaman `/login`.

---

## ⚡ Cara Menjalankan Lokal (PHP Native / SQLite / Termux)
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed --database=sqlite
php artisan serve
```

---

## 📜 Kepatuhan Standar Dokumen
- **SRS / PRD**: Sesuai ISO/IEC/IEEE 29148:2018 (`/sdcard/Download/PRD_SRS_Jasa_Website.pdf`).
- **Gated Release Security**: Standar OWASP & enkripsi AES-256-CBC.
- **Digital Handover**: Form Berita Acara Serah Terima (BAST) siap cetak / PDF.
