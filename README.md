# Gudang Pakde

Marketplace barang bekas untuk komunitas, dikembangkan sebagai **GITS Fullstack Developer Mini-Project Assignment**.

Pengguna dapat mencari barang, memasang dan mengelola listing, serta menghubungi penjual melalui WhatsApp.

## Dokumentasi Lengkap

**README ini hanya ringkasan. Untuk panduan lengkap, buka halaman [`/dokumentasi`](http://127.0.0.1:8000/dokumentasi) setelah aplikasi berjalan.** Sesuaikan host dan port dengan URL yang ditampilkan oleh `php artisan serve`.

Halaman tersebut memuat panduan instalasi, fitur aplikasi, desain database, kontrak route, keputusan teknologi, diagram arsitektur aplikasi dan infrastruktur, serta panduan deployment dan operasional.

Dokumen sumber juga tersedia di folder [`docs/`](docs/), antara lain:
- [Requirement dan scope MVP](docs/01-requirements.md)
- [Arsitektur aplikasi](docs/03-application-architecture.md)
- [Rancangan infrastruktur 100.000 DAU](docs/04-infrastructure-architecture.md)
- [Desain database](docs/05-database-design.md)
- [Dokumentasi route](docs/06-api-documentation.md)
- [Setup, deployment, dan operasional](docs/08-deployment-and-operations.md)

> Infrastruktur 100.000 DAU merupakan rancangan arsitektur, bukan klaim kapasitas yang sudah diuji atau di-deploy.

## Tech Stack

Laravel 13 · Vue 3 · Inertia.js · Tailwind CSS · MySQL 8.x, dengan pendekatan **Modular Monolith**.

## Fitur Utama

- Registrasi, login, logout, dan pengaturan profil.
- Katalog barang dengan pencarian, filter, dan paginasi.
- Detail barang, foto, kondisi, lokasi, dan informasi penjual.
- Pasang, edit, dan hapus listing dengan otorisasi pemilik.
- Status barang tersedia atau terjual.
- Kontak penjual melalui WhatsApp click-to-chat.

## Menjalankan Lokal

Siapkan PHP dan Node.js yang kompatibel dengan dependency proyek, Composer, npm, serta MySQL 8.x.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Buat database MySQL, lalu sesuaikan `APP_URL` dan konfigurasi `DB_*` di `.env`. Setelah itu:

```bash
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Untuk pengembangan frontend, jalankan `npm run dev` di terminal lain. Detail konfigurasi dan dataset demo dapat dilihat pada halaman **`/dokumentasi`**.

## Validasi

```bash
php artisan test
npm run build
```
