<?php

namespace Database\Seeders;

use App\Http\Controllers\LocationController;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProductListingSeeder extends Seeder
{
    /**
     * Seed 24 realistic community secondhand marketplace listings with real photos and API locations.
     */
    public function run(): void
    {
        // 1. Ambil data wilayah dan kota menggunakan Location API controller internal
        $request = new Request();
        $locationController = app(LocationController::class);
        $locationsResponse = $locationController->index($request);
        $allCities = $locationsResponse->getData(true) ?: [];

        $findLocation = function (string $keyword) use ($allCities): string {
            foreach ($allCities as $item) {
                if (isset($item['city']) && str_contains(strtolower($item['city']), strtolower($keyword))) {
                    return $item['label'] ?? ($item['city'].', '.$item['province']);
                }
            }

            // Fallback random city dari API
            if (! empty($allCities)) {
                $random = $allCities[array_rand($allCities)];

                return $random['label'] ?? ($random['city'].', '.$random['province']);
            }

            return 'Kota Bandung, Jawa Barat';
        };

        // 2. Siapkan 4 akun penjual komunitas aktif
        $sellers = [
            'budi@pakde.com' => User::updateOrCreate(
                ['email' => 'budi@pakde.com'],
                [
                    'name' => 'Pak Budi Santoso',
                    'phone' => '6281234567890',
                    'password' => Hash::make('password'),
                ]
            ),
            'joko@pakde.com' => User::updateOrCreate(
                ['email' => 'joko@pakde.com'],
                [
                    'name' => 'Pak Joko Widodo',
                    'phone' => '6281398765432',
                    'password' => Hash::make('password'),
                ]
            ),
            'tejo@pakde.com' => User::updateOrCreate(
                ['email' => 'tejo@pakde.com'],
                [
                    'name' => 'Pak Tejo Sutrisno',
                    'phone' => '6281567891234',
                    'password' => Hash::make('password'),
                ]
            ),
            'pakde@gudangpakde.id' => User::updateOrCreate(
                ['email' => 'pakde@gudangpakde.id'],
                [
                    'name' => 'Pakde Slamet',
                    'phone' => '6281288889999',
                    'password' => Hash::make('password'),
                ]
            ),
        ];

        // 3. Pastikan kategori sudah terisi
        $categories = Category::all()->keyBy('slug');
        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all()->keyBy('slug');
        }

        // 4. Daftar 24 produk barang bekas realistis dengan foto asli
        $products = [
            // --- KATEGORI: SEPEDA ---
            [
                'category' => 'sepeda',
                'title' => 'Sepeda Gunung Polygon Xtrada 6 29er 2023',
                'price' => 4850000,
                'condition' => 'good',
                'city_query' => 'Bandung',
                'status' => 'available',
                'views_count' => 142,
                'seller' => 'budi@pakde.com',
                'image' => 'sepeda-polygon-xtrada.jpg',
                'description' => 'Sepeda gunung Polygon Xtrada 6 ukuran ban 29 inci. Grupset Deore 1x11 speed perpindahan gigi masih empuk dan sangat presisi. Fork air suspension Suntour XCR lock aktif normal. Ban Maxxis Rekon Race masih tebal 85%. Dijual santai karena jarang gowes keluar kota lagi. Ada baret pemakaian wajar di crank, selebihnya mulus siap gowes blusukan.',
            ],
            [
                'category' => 'sepeda',
                'title' => 'Sepeda Lipat Brompton M6R Racing Green Original',
                'price' => 21500000,
                'condition' => 'like_new',
                'city_query' => 'Jakarta Selatan',
                'status' => 'available',
                'views_count' => 389,
                'seller' => 'joko@pakde.com',
                'image' => 'sepeda-lipat-brompton.jpg',
                'description' => 'Brompton M6R warna klasik Racing Green asli buatan London. 6 speed, rak belakang bagasi dan spakbor mudguard terpasang lengkap. Lipatan rapat, engsel kokoh no oblak. Pembelian tahun 2022, pemakaian hanya keliling komplek saat weekend. Surat manual book bawaan ada.',
            ],
            [
                'category' => 'sepeda',
                'title' => 'Road Bike Trek Domane AL 2 Disc Size 52',
                'price' => 8700000,
                'condition' => 'good',
                'city_query' => 'Surabaya',
                'status' => 'available',
                'views_count' => 210,
                'seller' => 'tejo@pakde.com',
                'image' => 'sepeda-roadbike-trek.jpg',
                'description' => 'Roadbike Trek Domane AL 2 Disc brake mekanik. Frame Alpha 100 aluminium ringan dengan fork karbon IsoSpeed peredam getaran. Grupset Shimano Claris 8 speed lancar. Cocok untuk tinggi badan 165-175 cm. Bartape baru diganti, rantai baru dilumasi. Siap pakai jalan raya.',
            ],
            [
                'category' => 'sepeda',
                'title' => 'Sepeda Minivelo Commuter 20 Inci Klasik Vintage',
                'price' => 1650000,
                'condition' => 'fair',
                'city_query' => 'Yogyakarta',
                'status' => 'available',
                'views_count' => 95,
                'seller' => 'pakde@gudangpakde.id',
                'image' => 'sepeda-minivelo-vintage.jpg',
                'description' => 'Sepeda minivelo klasik ring 20 inci cocok untuk santai keliling kota atau komuter ke kantor. Frame chromoly cat orisinal ada lecet vintage penuaan. Rem u-brake pakem, sadel per empuk kulit sintetis. Siap pakai harian keliling kampung.',
            ],
            [
                'category' => 'sepeda',
                'title' => 'Sepeda BMX United Roouse Pro 20 Inch Kuat',
                'price' => 1250000,
                'condition' => 'good',
                'city_query' => 'Tangerang',
                'status' => 'sold',
                'views_count' => 164,
                'seller' => 'budi@pakde.com',
                'image' => 'sepeda-bmx-united.jpg',
                'description' => 'Sepeda BMX United Roouse Pro ukuran 20 inci. Rangka besi kokoh tebal, rotor stang bisa putar 360 derajat. Ban jumbo tebal, velg alloy tapak lebar. Minus lecet wajar di fork depan karena pemakaian. Cocok buat anak muda atau latihan street.',
            ],

            // --- KATEGORI: PERKAKAS ---
            [
                'category' => 'perkakas',
                'title' => 'Mesin Bor Baterai Makita 18V Cordless DHP482',
                'price' => 1150000,
                'condition' => 'like_new',
                'city_query' => 'Semarang',
                'status' => 'available',
                'views_count' => 178,
                'seller' => 'joko@pakde.com',
                'image' => 'bor-cordless-makita.jpg',
                'description' => 'Mesin bor cordless Makita 18V LXT DHP482 impact drill (bisa bor beton, besi, kayu). Kelengkapan: 1 unit bor, 2 baterai 18V 3.0Ah, 1 fast charger, dan koper box orisinal. Baterai masih sangat awet tidak drop. Bekas proyek renovasi rumah sendiri saja.',
            ],
            [
                'category' => 'perkakas',
                'title' => 'Mesin Gerinda Tangan Bosch GWS 060 4 Inch 670W',
                'price' => 380000,
                'condition' => 'good',
                'city_query' => 'Bekasi',
                'status' => 'available',
                'views_count' => 87,
                'seller' => 'tejo@pakde.com',
                'image' => 'gerinda-bosch.jpg',
                'description' => 'Gerinda tangan Bosch GWS 060 daya 670 Watt, putaran mesin stabil dan halus. Masih original belum pernah servis atau ganti carbon brush. Termasuk kunci buka batu dan pelindung mata gerinda. Bonus 3 pcs batu potong wd tipis.',
            ],
            [
                'category' => 'perkakas',
                'title' => 'Set Kunci Sok Koper Tekiro 46 Pcs Lengkap Original',
                'price' => 420000,
                'condition' => 'new',
                'city_query' => 'Sidoarjo',
                'status' => 'available',
                'views_count' => 230,
                'seller' => 'pakde@gudangpakde.id',
                'image' => 'kunci-sok-tekiro.jpg',
                'description' => 'Kunci sok set Tekiro 46 pcs koper hijau chrome vanadium (Cr-V). Gagang rachet 1/4 inch, sambungan fleksibel, mata obeng lengkap. Kondisi baru belum pernah dipakai, hadiah doorprize kantor. Koper rapi dan mata kunci tidak ada yang hilang satu pun.',
            ],
            [
                'category' => 'perkakas',
                'title' => 'Chainsaw Gergaji Mesin Husqvarna 120 Mark II 16 Inch',
                'price' => 1850000,
                'condition' => 'good',
                'city_query' => 'Bogor',
                'status' => 'available',
                'views_count' => 115,
                'seller' => 'budi@pakde.com',
                'image' => 'chainsaw-husqvarna.jpg',
                'description' => 'Chainsaw bensin Husqvarna 120 Mark II panjang bar 16 inci. Tarikan enteng, oli rantai otomatis jalan lancar, rantai tajam baru diasah. Dipakai hanya untuk pangkas dahan pohon di kebun belakang rumah. Suara mesin padat bertenaga.',
            ],
            [
                'category' => 'perkakas',
                'title' => 'Tangga Lipat Teleskopik Aluminium 3.8 Meter Single',
                'price' => 650000,
                'condition' => 'good',
                'city_query' => 'Depok',
                'status' => 'sold',
                'views_count' => 143,
                'seller' => 'joko@pakde.com',
                'image' => 'tangga-teleskopik.jpg',
                'description' => 'Tangga teleskopik lurus panjang maksimal 3.8 meter bahan aluminium alloy tebal. Praktis bisa dipendekkan hingga 85 cm, mudah dibawa masuk bagasi mobil. Kunci otomatis tiap anak tangga aman dan kokoh. Mampu menahan beban hingga 150 kg.',
            ],

            // --- KATEGORI: HOBI ---
            [
                'category' => 'hobi',
                'title' => 'Gitar Akustik Yamaha F310 Natural Original Mulus',
                'price' => 950000,
                'condition' => 'good',
                'city_query' => 'Surakarta',
                'status' => 'available',
                'views_count' => 275,
                'seller' => 'tejo@pakde.com',
                'image' => 'gitar-yamaha-f310.jpg',
                'description' => 'Gitar akustik Yamaha F310 orisinal seri natural gloss. Neck lurus no buzzing, jarak senar rendah empuk dimainkan. Senar baru diganti D\'Addario 0.10. Ada sedikit goresan halus di pickguard karena pemakaian strumming. Bonus softcase busa tebal.',
            ],
            [
                'category' => 'hobi',
                'title' => 'Kamera DSLR Canon EOS 60D + Lensa Kit 18-55mm IS II',
                'price' => 3400000,
                'condition' => 'good',
                'city_query' => 'Malang',
                'status' => 'available',
                'views_count' => 320,
                'seller' => 'pakde@gudangpakde.id',
                'image' => 'kamera-canon-60d.jpg',
                'description' => 'Kamera semi-pro Canon EOS 60D layar lipat putar. Sensor bersih no jamur, shutter empuk responsif, karet jempol kencang no melar. Lensa kit 18-55mm autofocus lancar. Kelengkapan charger, baterai awet, tali strap dan tas selempang kamera.',
            ],
            [
                'category' => 'hobi',
                'title' => 'Tenda Camping Dome Eiger Shira 4 Orang Double Layer',
                'price' => 750000,
                'condition' => 'like_new',
                'city_query' => 'Denpasar',
                'status' => 'available',
                'views_count' => 198,
                'seller' => 'budi@pakde.com',
                'image' => 'tenda-camping-eiger.jpg',
                'description' => 'Tenda dome Eiger kapasitas 4 orang dengan vestibule depan luas. Double layer tahan air hujan deras (waterproof 3000mm). Frame fiber utuh no retak, pasak komplit, jahitan sealer rapi. Baru dipakai 2 kali kamping di Bedugul. Sangat bersih dan wangi.',
            ],
            [
                'category' => 'hobi',
                'title' => 'Set Joran Pancing Shimano Bassterra + Reel Daiwa 3000',
                'price' => 820000,
                'condition' => 'good',
                'city_query' => 'Cirebon',
                'status' => 'available',
                'views_count' => 156,
                'seller' => 'joko@pakde.com',
                'image' => 'joran-pancing-shimano.jpg',
                'description' => 'Set pancing siap pakai untuk kolam monster atau muara. Joran Shimano Bassterra XT panjang 180cm line 8-16 lbs cincin Fuji. Reel Daiwa Crossfire 3000 sudah terisi senar PE 2. Putaran reel halus no seret, drag klik nyaring.',
            ],
            [
                'category' => 'hobi',
                'title' => 'Raket Badminton Yonex Astrox 88D Pro 4UG5 Original',
                'price' => 1350000,
                'condition' => 'like_new',
                'city_query' => 'Medan',
                'status' => 'available',
                'views_count' => 215,
                'seller' => 'tejo@pakde.com',
                'image' => 'raket-yonex-astrox.jpg',
                'description' => 'Raket badminton Yonex Astrox 88D Pro kode SP orisinal (bukan KW). Berat 4U grip G5, head heavy mantap buat smash keras. Terpasang senar Yonex BG66 Ultimax tarikan 28 lbs digital. Tidak ada benturan atau retak sama sekali, frame mulus 98%.',
            ],

            // --- KATEGORI: ELEKTRONIK ---
            [
                'category' => 'elektronik',
                'title' => 'Smart TV LG 43 Inch 4K UHD 43UQ7500 ThinQ AI HDR',
                'price' => 2900000,
                'condition' => 'good',
                'city_query' => 'Jakarta Barat',
                'status' => 'available',
                'views_count' => 340,
                'seller' => 'pakde@gudangpakde.id',
                'image' => 'tv-lg-43inch.jpg',
                'description' => 'Smart TV LG 43 inch resolusi 4K UHD jernih. OS webOS responsif, aplikasi YouTube, Netflix, Disney+, Vidio jalan lancar. Dilengkapi Magic Remote dengan voice command pointer. Layar mulus no dead pixel no garis. Kaki TV dan remote orisinal komplit.',
            ],
            [
                'category' => 'elektronik',
                'title' => 'Speaker Monitor Studio Yamaha HS5 Sepasang Black',
                'price' => 3200000,
                'condition' => 'like_new',
                'city_query' => 'Makassar',
                'status' => 'available',
                'views_count' => 280,
                'seller' => 'budi@pakde.com',
                'image' => 'speaker-yamaha-hs5.jpg',
                'description' => 'Sepasang speaker active monitor Yamaha HS5 warna hitam elegan. Suara flat akurat untuk mixing musik, podcast, atau home recording. Driver tweeter dan woofer utuh tidak ada penyok. Kabel power dan kardus bawaan masih ada. Jarang dinyalakan volume tinggi.',
            ],
            [
                'category' => 'elektronik',
                'title' => 'Monitor Gaming LG UltraGear 24 Inch 144Hz IPS 24GN600',
                'price' => 1650000,
                'condition' => 'good',
                'city_query' => 'Surabaya',
                'status' => 'available',
                'views_count' => 260,
                'seller' => 'joko@pakde.com',
                'image' => 'monitor-lg-ultragear.jpg',
                'description' => 'Monitor gaming LG UltraGear 24GN600 panel IPS Full HD refresh rate 144Hz response time 1ms. Warna akurat sRGB 99% dan dukungan HDR10 & AMD FreeSync Premium. Port HDMI dan DisplayPort normal. No dead pixel, pemakaian apik untuk kerja dan game santai.',
            ],
            [
                'category' => 'elektronik',
                'title' => 'Headphone Wireless Sony WH-1000XM4 Noise Cancelling',
                'price' => 2450000,
                'condition' => 'good',
                'city_query' => 'Jakarta Pusat',
                'status' => 'sold',
                'views_count' => 410,
                'seller' => 'tejo@pakde.com',
                'image' => 'headphone-sony-xm4.jpg',
                'description' => 'Headphone premium Sony WH-1000XM4 peredam bising aktif (ANC) terbaik. Baterai tahan hingga 30 jam, suara bass mantap dan vokal jernih. Earpad kulit empuk nyaman dipakai berjam-jam. Kelengkapan: hardcase koper, kabel audio jack, dan adaptor pesawat.',
            ],
            [
                'category' => 'elektronik',
                'title' => 'Microwave Oven Sharp R-21D0(S)-IN 23 Liter 450W',
                'price' => 580000,
                'condition' => 'fair',
                'city_query' => 'Palembang',
                'status' => 'available',
                'views_count' => 112,
                'seller' => 'pakde@gudangpakde.id',
                'image' => 'microwave-sharp.jpg',
                'description' => 'Microwave Sharp kapasitas 23 liter hemat daya hanya 450 Watt. Pengaturan timer putar mekanik awet tidak mudah error. Piringan kaca putar di dalam mulus no sumbing. Fungsi memanaskan makanan dan defrost daging beku bekerja 100% normal.',
            ],

            // --- KATEGORI: RUMAH TANGGA ---
            [
                'category' => 'rumah-tangga',
                'title' => 'Meja Kerja Kayu Jati Solid Finishing Natural 120x60',
                'price' => 1200000,
                'condition' => 'good',
                'city_query' => 'Jepara',
                'status' => 'available',
                'views_count' => 185,
                'seller' => 'budi@pakde.com',
                'image' => 'meja-jati-solid.jpg',
                'description' => 'Meja kerja kayu jati asli perhutani ukuran panjang 120 cm lebar 60 cm tinggi 75 cm. Kayu tebal dan berat kokoh tidak goyang sama sekali. Serat kayu tampak indah dengan finishing melamic doff natural. Dilengkapi 2 laci penyimpanan di bagian bawah.',
            ],
            [
                'category' => 'rumah-tangga',
                'title' => 'Kursi Kantor Ergonomis Jaring Mesh dengan Headrest',
                'price' => 950000,
                'condition' => 'like_new',
                'city_query' => 'Jakarta Timur',
                'status' => 'available',
                'views_count' => 290,
                'seller' => 'joko@pakde.com',
                'image' => 'kursi-kantor-ergonomis.jpg',
                'description' => 'Kursi kerja ergonomis sandaran jaring breathable tidak panas di punggung. Ada lumbar support punggung bawah dan headrest yang bisa diatur tingginya. Hidrolik gaslift lancar naik turun, roda nylon mulus di lantai. Busa dudukan tebal no kempes.',
            ],
            [
                'category' => 'rumah-tangga',
                'title' => 'Mesin Kopi Espresso DeLonghi Dedica EC685 Red Edition',
                'price' => 1850000,
                'condition' => 'good',
                'city_query' => 'Bandung',
                'status' => 'available',
                'views_count' => 205,
                'seller' => 'tejo@pakde.com',
                'image' => 'mesin-kopi-delonghi.jpg',
                'description' => 'Mesin espresso rumahan DeLonghi Dedica EC685 warna merah cabe. Tekanan pompa 15 bar menghasilkan crema espresso tebal dan kaya. Steam wand bisa bikin microfoam susu untuk latte art. Termasuk portafilter, basket single dan double, tamper sendok.',
            ],
            [
                'category' => 'rumah-tangga',
                'title' => 'Kipas Angin Berdiri Besi Sekai 18 Inch HFN 1858 Kuat',
                'price' => 280000,
                'condition' => 'good',
                'city_query' => 'Bandar Lampung',
                'status' => 'sold',
                'views_count' => 125,
                'seller' => 'pakde@gudangpakde.id',
                'image' => 'kipas-angin-sekai.jpg',
                'description' => 'Kipas angin berdiri Sekai baling-baling besi ukuran 18 inci. Angin kencang dan hembusan luas bisa menjangkau seluruh ruang tamu. Tiang besi kokoh pemberat bulat stabil no getar. Motor dinamo tembaga dengan sekring pengaman panas (thermofuse).',
            ],
        ];

        // 5. Simpan dan replikasi file gambar ke Storage public agar sinkron
        $disk = Storage::disk('public');
        $imgSourceDir = public_path('img/products');

        foreach ($products as $item) {
            $seller = $sellers[$item['seller']];
            $category = $categories[$item['category']] ?? Category::first();
            $location = $findLocation($item['city_query']);
            $filename = $item['image'];
            $sourceFile = $imgSourceDir.'/'.$filename;

            // Pastikan file gambar ada di public/img/products
            if (! file_exists($sourceFile)) {
                throw new RuntimeException("File gambar tidak ditemukan di public/img/products/{$filename}");
            }

            // Copy juga ke storage disk public agar konsisten dengan upload user
            $storagePath = 'products/'.$filename;
            if (! $disk->exists($storagePath)) {
                $disk->put($storagePath, file_get_contents($sourceFile));
            }

            // Buat atau perbarui Listing
            $listing = Listing::updateOrCreate(
                [
                    'user_id' => $seller->id,
                    'title' => $item['title'],
                ],
                [
                    'category_id' => $category->id,
                    'price' => $item['price'],
                    'item_condition' => $item['condition'],
                    'location' => $location,
                    'status' => $item['status'],
                    'views_count' => $item['views_count'],
                    'description' => $item['description'],
                ]
            );

            // Perbarui gambar listing jika belum ada
            if ($listing->images()->count() === 0) {
                $listing->images()->create([
                    'path' => 'img/products/'.$filename,
                    'sort_order' => 0,
                ]);
            }
        }
    }
}
