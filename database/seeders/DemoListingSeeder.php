<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class DemoListingSeeder extends Seeder
{
    public const SELLER_EMAIL = 'demo-seller@gudang-pakde.example.test';

    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new RuntimeException('Demo listings may only be seeded in local or testing environments.');
        }

        $items = [
            ['sepeda', 'Sepeda Lipat 20 Inci untuk Komuter', 1450000, 'good', 'Bandung, Jawa Barat', 'Sepeda lipat 20 inci, tujuh percepatan. Rantai dan rem berfungsi baik; ada gores pemakaian pada rangka. Cocok untuk perjalanan pendek di kota.', 'sepeda-lipat.svg', 'available'],
            ['perkakas', 'Bor Listrik Kabel 550W dengan Koper', 320000, 'fair', 'Depok, Jawa Barat', 'Bor listrik 550W untuk pekerjaan rumah. Sudah dicoba dan berfungsi, casing memiliki bekas pakai. Termasuk koper dan mata bor seperti pada ilustrasi.', 'bor-listrik.svg', 'available'],
            ['hobi', 'Gitar Akustik Dreadnought Natural', 680000, 'good', 'Yogyakarta, DI Yogyakarta', 'Gitar akustik ukuran penuh dengan senar baru. Suara masih jernih; ada baret kecil di bagian belakang. Cocok untuk latihan di rumah.', 'gitar-akustik.svg', 'available'],
            ['elektronik', 'Kamera Mirrorless dengan Lensa Kit', 3950000, 'like_new', 'Jakarta Selatan, DKI Jakarta', 'Kamera mirrorless dengan lensa kit, baterai dan pengisi daya. Tombol, layar, dan fokus berfungsi normal; bodi terawat.', 'kamera.svg', 'sold'],
            ['rumah-tangga', 'Kursi Kerja Sandaran Jaring', 750000, 'good', 'Surabaya, Jawa Timur', 'Kursi kerja dengan sandaran jaring dan pengaturan tinggi. Roda lancar, dudukan kokoh, dan kain memiliki sedikit tanda pemakaian.', 'kursi-kerja.svg', 'available'],
            ['sepeda', 'Helm Sepeda Urban Ukuran M', 185000, 'new', 'Semarang, Jawa Tengah', 'Helm sepeda ukuran M dengan pengaturan lingkar kepala. Belum dipakai, masih dengan label; ukuran dan kecocokan perlu dicek sebelum membeli.', 'helm-sepeda.svg', 'sold'],
        ];

        // Fail before changing the database when a bundled asset is missing.
        foreach ($items as $item) {
            if (! is_file(public_path('demo/'.$item[6]))) {
                throw new RuntimeException('Missing bundled demo image: '.$item[6]);
            }
        }

        $seller = User::where('email', self::SELLER_EMAIL)->first();
        if ($seller && ($seller->name !== 'Penjual Demo Gudang Pakde' || $seller->phone !== null)) {
            throw new RuntimeException('Demo seller email is already used by a different account.');
        }

        $seller ??= User::create([
            'name' => 'Penjual Demo Gudang Pakde',
            'email' => self::SELLER_EMAIL,
            'phone' => null,
            // Random, unpublished secret: this account cannot be logged into using a shared demo password.
            'password' => Hash::make(Str::random(64)),
        ]);

        foreach ($items as [$slug, $title, $price, $condition, $location, $description, $filename, $status]) {
            $category = Category::firstOrCreate(['slug' => $slug], ['name' => [
                'sepeda' => 'Sepeda', 'perkakas' => 'Perkakas', 'hobi' => 'Hobi',
                'elektronik' => 'Elektronik', 'rumah-tangga' => 'Rumah Tangga',
            ][$slug]]);

            // Include trashed rows so a deliberately removed demo item stays removed on rerun.
            $listing = Listing::withTrashed()->firstOrCreate(
                ['user_id' => $seller->id, 'title' => $title],
                ['category_id' => $category->id, 'price' => $price, 'item_condition' => $condition,
                    'description' => $description, 'location' => $location, 'status' => $status],
            );

            if ($listing->trashed() || $listing->images()->exists()) {
                continue;
            }

            $path = 'demo/gudang-pakde/'.$filename;
            $disk = Storage::disk('public');
            if (! $disk->exists($path) && ! $disk->put($path, file_get_contents(public_path('demo/'.$filename)))) {
                throw new RuntimeException('Could not store demo image: '.$path);
            }

            $listing->images()->create(['path' => $path, 'sort_order' => 0]);
        }
    }
}
