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
        Category::insert([[
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
            'category_name' => 'Lowongan Kerja',
            'slug' => 'loker',
        ],
        [
            'category_name' => 'Lainnya',
            'slug' => 'lainnya',
        ]]);
    }
}
