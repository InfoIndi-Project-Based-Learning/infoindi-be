<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan ada user dummy dan admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin InfoIndi',
                'username' => 'admin_infoindi',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'test_user',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        // Buat beberapa user dummy tambahan jika belum ada cukup user
        if (User::count() < 5) {
            User::factory(5)->create([
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        $users = User::all();

        // 2. Ambil semua kategori dari database
        $categories = Category::all()->keyBy('slug');

        if ($categories->isEmpty()) {
            $this->command->warn('Kategori kosong! Silakan jalankan CategorySeeder terlebih dahulu.');
            return;
        }

        // 3. Data postingan dengan banner_url berkualitas tinggi dari Unsplash (internet)
        $postsData = [
            [
                'post_name' => 'Sepatu Sneakers Adidas Originals - Bekas Terawat',
                'description' => 'Jual santai sneakers Adidas Originals size 42. Kondisi sangat mulus 95%, box lengkap, pemakaian wajar. COD sekitaran Bandung Kota atau kirim-kirim via kurir. Hubungi saya jika berminat!',
                'banner_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'jualan',
                'view_count' => 128,
                'additional_images' => [
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?auto=format&fit=crop&w=800&q=80',
                ]
            ],
            [
                'post_name' => 'Keyboard Mechanical Keychron K2 V2 Gateron Brown',
                'description' => 'Dijual Keychron K2 V2, Gateron Brown Switch, White Backlight. Baterai masih awet banget, dus dan keycap puller lengkap bawaan. Alasan jual karena sudah upgrade ke keyboard lain.',
                'banner_url' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'jualan',
                'view_count' => 84,
                'additional_images' => [
                    'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&w=800&q=80'
                ]
            ],
            [
                'post_name' => 'Jasa Desain Grafis & Branding Professional UMKM',
                'description' => 'Menerima jasa desain logo, poster, kebutuhan feeds Instagram bisnis, banner, dan identitas brand lengkap. Pengerjaan cepat, revisi up to 3x. Harga sangat bersahabat bagi pegiat UMKM!',
                'banner_url' => 'https://images.unsplash.com/photo-1521791136364-798a7bc0d220?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'jasa',
                'view_count' => 245,
                'additional_images' => []
            ],
            [
                'post_name' => 'Jasa Service AC Panggilan Jabodetabek - Bergaransi',
                'description' => 'AC rumah Anda kurang dingin atau bocor? Kami siap melayani service AC, isi freon, bongkar pasang, dan cuci AC berkala. Teknisi handal, sopan, jujur dan bersertifikasi profesional.',
                'banner_url' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'jasa',
                'view_count' => 95,
                'additional_images' => []
            ],
            [
                'post_name' => 'National Business Plan Competition 2026',
                'description' => 'Ikuti ajang bergengsi kompetisi rencana bisnis tingkat nasional dengan total hadiah puluhan juta rupiah + trofi penghargaan. Pendaftaran dibuka gratis untuk seluruh mahasiswa aktif di seluruh Indonesia!',
                'banner_url' => 'https://images.unsplash.com/photo-1567427017947-545c5f8d16ad?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'info-lomba',
                'view_count' => 412,
                'additional_images' => [
                    'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=800&q=80'
                ]
            ],
            [
                'post_name' => 'Hackathon Innovation Challenge: Green Tech Indonesia',
                'description' => 'Tantang dirimu dan timmu untuk membangun solusi teknologi inovatif bertema kelestarian lingkungan dalam waktu 48 jam nonstop! Dapatkan mentoring langsung dari startup tech leaders.',
                'banner_url' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'info-lomba',
                'view_count' => 310,
                'additional_images' => []
            ],
            [
                'post_name' => 'Lowongan Kerja: Social Media Specialist (Full-time)',
                'description' => 'Dibutuhkan Social Media Specialist berbakat untuk mengelola konten, strategi kreatif, dan digital marketing campaign brand kami. Lokasi penempatan Jakarta Selatan, minimum pengalaman 1 tahun.',
                'banner_url' => 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'loker',
                'view_count' => 520,
                'additional_images' => []
            ],
            [
                'post_name' => 'Lowongan Kerja: Junior Web Developer (React / Tailwind)',
                'description' => 'Kami sedang mencari Junior Web Developer antusias yang menguasai React.js, TailwindCSS, dan integrasi API RESTful. Fresh graduate dengan portfolio menarik sangat dipersilakan melamar!',
                'banner_url' => 'https://images.unsplash.com/photo-1542744094-3a31f103e35f?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'loker',
                'view_count' => 610,
                'additional_images' => []
            ],
            [
                'post_name' => 'Komunitas Fotografi Bandung: Kumpul Santai & Hunting Foto',
                'description' => 'Mari kumpul akhir pekan sesama pecinta seni fotografi di area Bandung. Agenda kita adalah hunting street photography bersama di sepanjang jalan Braga dilanjutkan sesi sharing kopi santai.',
                'banner_url' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'lainnya',
                'view_count' => 143,
                'additional_images' => [
                    'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80'
                ]
            ],
            [
                'post_name' => 'Macbook Pro M1 2020 8/256GB - Fullset',
                'description' => 'Dijual cepat Macbook Pro M1 2020 varian 8/256GB. Kondisi mulus, battery health masih 92%. Kelengkapan fullset original. Cocok untuk ngoding atau desain.',
                'banner_url' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'jualan',
                'view_count' => 890,
                'additional_images' => [
                    'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=800&q=80'
                ]
            ],
            [
                'post_name' => 'Kamera Mirrorless Sony A6000 + Lensa Kit',
                'description' => 'Jual santai Sony A6000 beserta lensa kit 16-50mm. SC masih di bawah 10rb. Bonus tas kamera, memory card 32GB, dan 2 baterai cadangan. Hasil foto masih sangat tajam.',
                'banner_url' => 'https://images.unsplash.com/photo-1516724562728-afc824a36e84?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'jualan',
                'view_count' => 450,
                'additional_images' => []
            ],
            [
                'post_name' => 'Jasa Pembuatan Website Company Profile & Toko Online',
                'description' => 'Butuh website untuk bisnis Anda? Kami menyediakan jasa pembuatan website company profile, landing page, dan toko online dengan desain modern, responsif, dan SEO friendly. Harga terjangkau, gratis domain dan hosting 1 tahun.',
                'banner_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'jasa',
                'view_count' => 670,
                'additional_images' => []
            ],
            [
                'post_name' => 'Jasa Penerjemah Bahasa Inggris - Dokumen & Jurnal',
                'description' => 'Menerima jasa terjemahan Bahasa Inggris ke Bahasa Indonesia atau sebaliknya. Cocok untuk dokumen akademik, jurnal, artikel, atau dokumen bisnis. Dikerjakan oleh translator berpengalaman dengan hasil akurat dan natural.',
                'banner_url' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'jasa',
                'view_count' => 320,
                'additional_images' => []
            ],
            [
                'post_name' => 'Olimpiade Matematika Nasional 2026 (SD/SMP/SMA)',
                'description' => 'Hadirilah Olimpiade Matematika tingkat Nasional tahun 2026! Ajang pembuktian prestasi siswa dari seluruh Indonesia. Total hadiah puluhan juta rupiah dan sertifikat tingkat nasional. Segera daftarkan sekolahmu!',
                'banner_url' => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'info-lomba',
                'view_count' => 1200,
                'additional_images' => []
            ],
            [
                'post_name' => 'Lomba Menulis Cerpen Nasional 2026 - Tema Bebas',
                'description' => 'Salurkan bakat menulismu di Lomba Menulis Cerpen Tingkat Nasional 2026. Tema bebas, tidak mengandung SARA. Karya terbaik akan dibukukan dalam antologi cerpen. Pendaftaran gratis!',
                'banner_url' => 'https://images.unsplash.com/photo-1455390582262-044cdead2708?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'info-lomba',
                'view_count' => 845,
                'additional_images' => []
            ],
            [
                'post_name' => 'Lowongan Kerja: Barista Coffee Shop Part-time',
                'description' => 'Dibutuhkan segera Barista Part-time untuk kedai kopi baru di pusat kota. Syarat: Pria/Wanita usia 18-25 tahun, ramah, jujur, bersedia kerja shift malam. Pengalaman tidak diutamakan, ada training.',
                'banner_url' => 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'loker',
                'view_count' => 590,
                'additional_images' => []
            ],
            [
                'post_name' => 'Lowongan Kerja: Graphic Designer Internship',
                'description' => 'Buka kesempatan magang sebagai Graphic Designer (Internship) selama 3 bulan. Mahasiswa tingkat akhir atau fresh graduate dipersilakan melamar. Menguasai Adobe Illustrator & Photoshop.',
                'banner_url' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'loker',
                'view_count' => 730,
                'additional_images' => []
            ],
            [
                'post_name' => 'Bakti Sosial Donor Darah 2026 - PMI Kota',
                'description' => 'Mari selamatkan nyawa! Ikuti kegiatan donor darah massal yang diselenggarakan bekerja sama dengan PMI kota. Setetes darahmu sangat berarti bagi mereka yang membutuhkan. Ada bingkisan menarik untuk pendonor.',
                'banner_url' => 'https://images.unsplash.com/photo-1536856136534-bb679c52a9aa?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'lainnya',
                'view_count' => 410,
                'additional_images' => []
            ],
            [
                'post_name' => 'Info Kehilangan Dompet di Sekitaran Alun-alun',
                'description' => 'Mohon bantuannya, telah hilang dompet kulit warna coklat di sekitar area alun-alun kota pada hari Minggu sore. Berisi KTP, SIM, STNK, dan beberapa kartu ATM. Bagi yang menemukan harap hubungi nomor di bawah ini.',
                'banner_url' => 'https://images.unsplash.com/photo-1627522460108-215683bdc9ee?auto=format&fit=crop&w=1200&q=80',
                'category_slug' => 'lainnya',
                'view_count' => 205,
                'additional_images' => []
            ],
        ];

        // 4. Input postingan ke database
        foreach ($postsData as $index => $data) {
            $category = $categories->get($data['category_slug']);
            if (!$category) {
                continue;
            }

            // Ambil user secara acak (selain admin untuk regular posts)
            $author = $users->where('role', 'user')->random();

            $post = Post::create([
                'category_id' => $category->id,
                'user_id' => $author->id,
                'post_name' => $data['post_name'],
                'description' => $data['description'],
                'banner_url' => $data['banner_url'],
                'view_count' => $data['view_count'],
            ]);

            // 5. Tambahkan additional images jika ada
            foreach ($data['additional_images'] as $order => $imageUrl) {
                PostImage::create([
                    'post_id' => $post->id,
                    'image_url' => $imageUrl,
                    'order' => $order,
                ]);
            }

            // 6. Tambahkan likes acak dari user lain
            $likeCount = rand(3, $users->count());
            $likers = $users->shuffle()->take($likeCount);
            foreach ($likers as $liker) {
                \App\Models\PostLike::create([
                    'post_id' => $post->id,
                    'user_id' => $liker->id,
                ]);
            }
        }

        // Generate 30 additional random posts using Faker
        $faker = \Faker\Factory::create('id_ID');
        $unsplashIds = [
            '1542291026-7eec264c27ff', '1587829741301-dc798b83add3', '1521791136364-798a7bc0d220', 
            '1581578731548-c64695cc6952', '1567427017947-545c5f8d16ad', '1531482615713-2afd69097998', 
            '1586281380349-632531db7ed4', '1542744094-3a31f103e35f', '1506744038136-46273834b3fb',
            '1517336714731-489689fd1ca8', '1516724562728-afc824a36e84', '1460925895917-afdab827c52f',
            '1456513080510-7bf3a84b82f8', '1509228468518-180dd4864904', '1455390582262-044cdead2708',
            '1497935586351-b67a49e012bf', '1561070791-2526d30994b5', '1536856136534-bb679c52a9aa'
        ];

        for ($i = 0; $i < 30; $i++) {
            $category = $categories->random();
            // Fallback to any user if somehow no 'user' role is found (though we seeded 5 users)
            $authors = $users->where('role', 'user');
            $author = $authors->count() > 0 ? $authors->random() : $users->random();
            
            $post = Post::create([
                'category_id' => $category->id,
                'user_id' => $author->id,
                'post_name' => rtrim($faker->sentence(mt_rand(4, 8)), '.'),
                'description' => $faker->paragraphs(mt_rand(2, 4), true),
                'banner_url' => 'https://images.unsplash.com/photo-' . $faker->randomElement($unsplashIds) . '?auto=format&fit=crop&w=1200&q=80',
                'view_count' => mt_rand(10, 1500),
            ]);

            $likeCount = rand(1, max(1, $users->count() - 1));
            $likers = $users->shuffle()->take($likeCount);
            foreach ($likers as $liker) {
                \App\Models\PostLike::firstOrCreate([
                    'post_id' => $post->id,
                    'user_id' => $liker->id,
                ]);
            }
        }

        $this->command->info('PostSeeder berhasil dijalankan dengan gambar Unsplash berkualitas tinggi dan 30 data dinamis!');
    }
}
