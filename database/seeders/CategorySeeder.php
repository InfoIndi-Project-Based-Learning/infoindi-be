<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Jualan',
                'slug' => 'jualan',
            ],
            [
                'category_name' => 'Jasa',
                'slug' => 'jasa',
            ],
            [
                'category_name' => 'Info Lomba',
                'slug' => 'info-lomba',
            ],
            [
                'category_name' => 'Lowongan Pekerjaan',
                'slug' => 'lowongan-pekerjaan',
            ],
            [
                'category_name' => 'Lainnya',
                'slug' => 'lainnya',
            ]
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['category_name' => $category['category_name']]
            );
        }
    }
}
