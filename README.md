# Gudang Pakde

Marketplace barang bekas untuk komunitas — GITS Fullstack Developer mini-project.

## Stack
Laravel 13 · Vue 3 · Inertia.js · Tailwind CSS · MySQL 8.x (InnoDB). Architecture: modular monolith.

## Documentation
- [`AGENTS.md`](AGENTS.md): coding-agent rules and reading order
- [`docs/01-requirements.md`](docs/01-requirements.md): features, scope, acceptance
- [`docs/02-tech-stack-decisions.md`](docs/02-tech-stack-decisions.md): choices and trade-offs
- [`docs/03-application-architecture.md`](docs/03-application-architecture.md): modules and request flow
- [`docs/04-infrastructure-architecture.md`](docs/04-infrastructure-architecture.md): design for 100K DAU
- [`docs/05-database-design.md`](docs/05-database-design.md): schema and ERD
- [`docs/06-api-documentation.md`](docs/06-api-documentation.md): Inertia route contracts
- [`docs/07-scaling-and-security.md`](docs/07-scaling-and-security.md): performance and security
- [`docs/08-deployment-and-operations.md`](docs/08-deployment-and-operations.md): setup and operations
- [`docs/09-ui-ux-design-system.md`](docs/09-ui-ux-design-system.md): UI tokens, components, responsive behavior
- [`docs/10-page-specifications.md`](docs/10-page-specifications.md): page wireframes, user journeys, acceptance

## Menjalankan lokal

Prasyarat: PHP/Composer, Node.js/npm, dan MySQL 8.x. Salin `.env.example` ke `.env`, atur `APP_NAME="Gudang Pakde"`, `DB_CONNECTION=mysql` beserta `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` untuk database MySQL lokal yang sudah dibuat. Jangan gunakan kredensial produksi di repository.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# Atur DB_* di .env lalu buat database MySQL lokal
php artisan migrate --seed
php artisan storage:link
php artisan test
npm run build
php artisan serve
```

Untuk mengembangkan UI, jalankan `npm run dev` di terminal lain. Pengujian menggunakan SQLite in-memory sebagaimana diatur pada `phpunit.xml`; aplikasi lokal menggunakan driver yang dikonfigurasi di `.env`. Nomor WhatsApp penjual diatur melalui Profil dengan format internasional `62...` atau `+62...`; nomor wajib sebelum memasang barang. Barang terjual tetap tampil, tetapi tidak bisa dihubungi melalui CTA. Akun dengan riwayat barang (termasuk soft-deleted) tidak dapat dihapus.

## Status

FR-01–FR-09 telah diimplementasikan pada aplikasi Laravel/Inertia, dengan satu foto per barang dan kategori dari seeder. Route dan props aktual didokumentasikan di [`docs/06-api-documentation.md`](docs/06-api-documentation.md). Desain infrastruktur 100K DAU hanya dokumentasi, belum di-deploy atau dibenchmark. Migrasi dan seeder berhasil dijalankan di MySQL lokal terkonfigurasi. UI kini mengikuti arah visual Sellora yang diberikan pengguna: panel putih pada canvas abu-abu, navigasi pill, CTA charcoal, dan aksen ungu. Review Chrome untuk katalog, detail, create/edit, Barang Saya, profil, login/register sudah dilakukan pada 360/768/1440 px. Screenshot dan hasil ukur lokal berada di `storage/app/ui-review/sellora-*.png` dan `sellora-measurements.json` (artefak development yang tidak dilacak Git). Belum ada publikasi demo atau screenshot ke GitHub.

## Dataset demo (opsional, hanya lokal/testing)

Setelah migrasi dan `php artisan storage:link`, jalankan **secara eksplisit** di lingkungan lokal/testing:

```bash
php artisan db:seed --class=DemoListingSeeder
```

Perintah ini tidak termasuk `migrate --seed`: membuat satu akun sintetis `demo-seller@gudang-pakde.example.test`, enam contoh barang dari lima kategori, dan menyalin ilustrasi ke public disk (`storage/app/public/demo/gudang-pakde/`). Aman diulang tanpa menghapus/mengubah barang atau foto pengguna; perubahan dan penghapusan barang demo juga dipertahankan. Seeder menolak lingkungan selain `local`/`testing`. Gambar di `public/demo/*.svg` adalah **ilustrasi vektor orisinal yang dibuat untuk repository ini**, bukan foto barang nyata atau aset pihak ketiga; tidak memakai hotlink. Harga/kondisi/deskripsi adalah contoh fiktif, bukan penawaran sungguhan. Akun demo tidak memiliki nomor telepon, sehingga tombol WhatsApp **tidak aktif**; sandi dibuat acak saat seeding dan tidak dibagikan sebagai kredensial login. SVG hanya dipakai oleh seeder, bukan sebagai format upload pengguna (upload tetap JPEG/PNG/WebP). Jangan jalankan seeder pada database yang tidak dimaksudkan untuk demo.
