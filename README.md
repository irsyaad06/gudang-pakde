# Gudang Pakde

Marketplace barang bekas khusus komunitas — **GITS Fullstack Developer Mini-Project Assignment**.

Repository: [https://github.com/irsyaad06/gudang-pakde.git](https://github.com/irsyaad06/gudang-pakde.git)

---

## Tech Stack
- **Backend:** Laravel 13 (PHP 8.2+ / PHP 8.4)
- **Frontend:** Vue 3 (Composition API) + Inertia.js
- **Styling & UI:** Tailwind CSS v4 (Sellora-inspired clean surface design system)
- **Database:** MySQL 8.x (InnoDB, utf8mb4)
- **Pattern:** Modular Monolith (Single deployment unit, ACID database transactions, zero inter-service network overhead)

---

## Dokumentasi Lengkap & Arsitektur In-App
Aplikasi ini dilengkapi dengan **Halaman Dokumentasi Interaktif In-App** di [`/dokumentasi`](http://127.0.0.1:8000/dokumentasi) yang dapat diakses secara langsung melalui tombol melayang (*floating document button*) di pojok kanan bawah aplikasi.

Dokumentasi terbagi menjadi **2 Kategori Induk (*Parent Groups*)** sesuai spesifikasi assignment GITS:
1. 🏗️ **Rancangan Arsitektur Aplikasi & Infrastruktur (Skala 100.000 DAU)** — *Poin 2 Assignment*:
   - **Kalkulasi Beban & Kapasitas Matematis:** Estimasi ~3 juta request/hari, rata-rata ~35 RPS, peak hours 150–200 RPS, dan storage 15–25 GB/hari.
   - **Diagram Arsitektur Aplikasi:** Visual alur pengguna, Vue 3 + Inertia SPA, Edge CDN, Ingress ALB, Modular Monolith Laravel 13, Database/Cache, Storage S3, dan WhatsApp *click-to-chat*. Disertai kode Mermaid `.mmd`.
   - **Diagram Infrastruktur Cloud:** Topologi cloud Multi-AZ AWS VPC terisolasi (Cloudflare WAF, ALB, ECS Fargate 3–20 tasks, RDS MySQL Primary & Read Replica, ElastiCache Redis, S3, dan Datadog). Disertai kode Mermaid `.mmd`.
   - **Strategi Scaling, Reliability & Keamanan:** Auto-scaling, RDS connection pooling, caching bertingkat, rate limiting, isolasi subnet tertutup, dan Disaster Recovery (RPO < 5 mnt, RTO < 30 mnt).
2. 📘 **Technical Documentation** — *Poin 3 Assignment*:
   - **Ikhtisar Proyek & Matriks Fitur:** Latar belakang masalah komunitas dan pemenuhan 9 fitur MVP (100% Selesai).
   - **Panduan Setup & Instalasi Lokal:** Langkah instalasi macOS & Windows lengkap dengan akun demo seeder.
   - **Desain Database & ERD:** Diagram relasi 4 entitas utama, foreign keys, cascade deletes, dan strategi *composite indexing* performa tinggi (`idx_listings_catalog` dan `idx_listings_user`).
   - **Keputusan Tech Stack:** Alasan pemilihan stack dan komparasi vs decoupled SPA (Next.js + REST API).
   - **Dokumentasi Route Web & Keamanan:** Tabel kontrak HTTP endpoint, middleware guard, dan matriks izin `ListingPolicy`.
   - **Deployment & Operasional:** CI/CD pipeline, optimasi cache produksi, structured logging, dan health check `/up`.

Dokumentasi teknis spesifik juga tersedia pada folder `docs/`:
- [`AGENTS.md`](AGENTS.md): Aturan pengerjaan dan standar coding agent
- [`docs/01-requirements.md`](docs/01-requirements.md): Spesifikasi fitur dan kriteria penerimaan MVP
- [`docs/02-tech-stack-decisions.md`](docs/02-tech-stack-decisions.md): Keputusan teknologi dan pertimbangan trade-off
- [`docs/03-application-architecture.md`](docs/03-application-architecture.md): Arsitektur aplikasi, alur data, dan request lifecycle
- [`docs/04-infrastructure-architecture.md`](docs/04-infrastructure-architecture.md): Desain infrastruktur produksi untuk 100.000 DAU
- [`docs/05-database-design.md`](docs/05-database-design.md): Skema database, indeks performa, dan diagram relasi (ERD)
- [`docs/06-api-documentation.md`](docs/06-api-documentation.md): Kontrak route web dan endpoint Inertia
- [`docs/07-scaling-and-security.md`](docs/07-scaling-and-security.md): Keamanan data, proteksi CSRF/XSS, dan strategi scaling
- [`docs/08-deployment-and-operations.md`](docs/08-deployment-and-operations.md): Panduan deployment, CI/CD, dan operasional
- [`docs/09-ui-ux-design-system.md`](docs/09-ui-ux-design-system.md): Token visual UI, komponen, dan responsivitas
- [`docs/10-page-specifications.md`](docs/10-page-specifications.md): Wireframe halaman dan alur pengalaman pengguna

---

## Fitur Utama yang Telah Diimplementasikan (100% Selesai)

1. **Autentikasi Pengguna:** Registrasi, Login, dan Logout berbasis sesi dengan proteksi CSRF dan password hashing bcrypt.
2. **Profil & Barang Saya:** Manajemen informasi profil (nama, email, nomor WhatsApp internasional `+62`), serta tab riwayat listing barang pribadi dilengkapi indikator jumlah dilihat (*views count*).
3. **Katalog Barang Komunitas:** Grid responsif (1 kolom di HP, 2 di tablet, 3-4 di desktop) dengan gambar, harga terformat ribuan titik, kondisi barang, lokasi kota/provinsi, dan badge status barang.
4. **Pencarian & Multi-Filter:** Pencarian kata kunci real-time, filter kategori, filter kondisi (Baru, Seperti Baru, Bagus, Cukup), serta dropdown wilayah Indonesia (34 provinsi & 514 kota).
5. **Urutan Sortir Cerdas:** Secara default memprioritaskan barang yang **Tersedia**, lalu barang yang **Paling Banyak Dilihat**, dan barang yang **Terbaru** (`views_count DESC, created_at DESC`).
6. **Detail Barang & Galeri Multi-Foto:** Galeri interaktif dengan thumbnail switcher (hingga 5 foto per barang), deskripsi lengkap, kartu penjual, alamat lokasi, dan tombol WhatsApp kontak penjual.
7. **Pasang Iklan Barang:** Form pasang barang dengan validasi server-side, pemformat otomatis titik ribuan live (`1230000` -> `1.230.000`), integrasi API wilayah Indonesia, dan upload multi-foto (JPEG/PNG/WebP, max 2MB/foto).
8. **Kelola Listing & Keamanan Kebijakan:** Edit data barang, hapus foto individual, tambah foto baru, dan hapus listing dengan otorisasi ketat `ListingPolicy` (403 Forbidden bila bukan pemilik).
9. **Toggle Status Barang:** Penjual dapat mengubah status dari *Tersedia* ke *Terjual* dalam 1-klik dengan badge merah otomatis.
10. **Kontak WhatsApp Click-to-Chat:** Tombol bulat dengan ikon WhatsApp dan efek hover `#25D366` yang otomatis membuka chat dengan pesan pre-filled berisi nama barang dan tautan listing.
11. **Toast Notification:** Notifikasi toast melayang di pojok kanan atas untuk semua aksi berhasil maupun gagal.
12. **Navigasi Sliding Pill:** Indikator tab aktif dengan animasi pergeseran halus (*smooth sliding transition*).

---

## Panduan Menjalankan Aplikasi Secara Lokal

### Prasyarat
- PHP >= 8.2 (Disarankan PHP 8.4)
- Composer 2.x
- Node.js >= 20.x & npm
- MySQL Server 8.x
- Git

### Langkah Instalasi (macOS & Linux)
```bash
# 1. Clone repository
git clone https://github.com/irsyaad06/gudang-pakde.git
cd gudang-pakde

# 2. Instal dependensi backend dan frontend
composer install
npm install

# 3. Konfigurasi file environment
cp .env.example .env
php artisan key:generate

# 4. Buat database MySQL lokal (misal: gudang_pakde) dan sesuaikan DB_* di file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=gudang_pakde
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Jalankan migrasi database dan seeder
php artisan migrate --seed

# 6. Buat symlink storage foto barang
php artisan storage:link

# 7. Jalankan automated test
php artisan test

# 8. Kompilasi asset frontend
npm run build

# 9. Jalankan server pengembangan
php artisan serve
# Pada terminal lain:
npm run dev
```

### Langkah Instalasi (Microsoft Windows)
```cmd
:: 1. Clone repository
git clone https://github.com/irsyaad06/gudang-pakde.git
cd gudang-pakde

:: 2. Instal dependensi backend dan frontend
composer install
npm install

:: 3. Konfigurasi file environment
copy .env.example .env
php artisan key:generate

:: 4. Buat database MySQL lokal (misal: gudang_pakde) dan sesuaikan DB_* di file .env

:: 5. Jalankan migrasi dan seeder
php artisan migrate --seed

:: 6. Buat symlink storage foto barang
php artisan storage:link

:: 7. Jalankan automated test
php artisan test

:: 8. Kompilasi asset frontend
npm run build

:: 9. Jalankan server backend dan frontend
php artisan serve
:: Buka command prompt/terminal kedua:
npm run dev
```

Akses aplikasi di browser melalui: `http://127.0.0.1:8000`

---

## Status Pengujian Otomatis (Automated Tests)
Aplikasi dilengkapi dengan rangkaian pengujian fitur otomatis (Feature Tests) menyeluruh yang mencakup:
- Autentikasi Pengguna & Sesi
- Otorisasi Kebijakan `ListingPolicy` (Hak Akses Pemilik vs Pengguna Lain)
- Katalog, Pencarian Kata Kunci, Filter Kategori, Filter Kondisi, dan Filter Wilayah
- Prioritas Urutan Sortir (`available`, `views_count DESC`, `created_at DESC`)
- Increment Penghitung Dilihat (`views_count`)
- Upload Multi-Foto & Symlink Storage
- API Wilayah Indonesia (`/api/locations`)
- Halaman Dokumentasi Interaktif (`/dokumentasi`)

Hasil eksekusi `php artisan test`:
```text
PASS  Tests\Feature\Auth\AuthenticationTest
PASS  Tests\Feature\Auth\EmailVerificationTest
PASS  Tests\Feature\Auth\PasswordConfirmationTest
PASS  Tests\Feature\Auth\PasswordResetTest
PASS  Tests\Feature\Auth\PasswordUpdateTest
PASS  Tests\Feature\Auth\RegistrationTest
PASS  Tests\Feature\DemoListingSeederTest
PASS  Tests\Feature\DocsTest
PASS  Tests\Feature\ExampleTest
PASS  Tests\Feature\MarketplaceTest
PASS  Tests\Feature\ProfileTest

Tests:    41 passed (492 assertions)
Duration: 1.98s
```

---

## Akun Demo
- **Email:** `pakde@gudangpakde.id`
- **Password:** `password`
- Seeder opsional: `php artisan db:seed --class=DemoListingSeeder` (membuat listing demo dengan ilustrasi vektor orisinal).
