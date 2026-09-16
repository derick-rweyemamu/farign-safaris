<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Safari',
                'slug' => Str::slug('Safari'),
                'description' => 'Explore national parks and wildlife adventures.',
                'is_active' => true,
                'image' => 'categories/safari.jpg',
            ],
            [
                'name' => 'Hiking',
                'slug' => Str::slug('Hiking'),
                'description' => 'Challenging hiking experiences for thrill seekers.',
                'is_active' => true,
                'image' => 'categories/hiking.jpg',
            ],
        ];
        foreach ($categories as $category) {
            \App\Models\ProductCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
