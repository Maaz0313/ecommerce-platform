<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
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
                'name' => 'Electronics',
                'description' => 'Electronic gadgets and devices including smartphones, laptops, and accessories.'
            ],
            [
                'name' => 'Clothing',
                'description' => 'Men\'s, women\'s, and children\'s apparel including shirts, pants, dresses, and activewear.'
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Everything for your home including furniture, kitchenware, and home decor.'
            ],
            [
                'name' => 'Books',
                'description' => 'Fiction, non-fiction, textbooks, and e-books across various genres and topics.'
            ],
            [
                'name' => 'Beauty & Personal Care',
                'description' => 'Skincare, makeup, haircare, and personal grooming products.'
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Equipment and apparel for sports, outdoor activities, and fitness.'
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description']
            ]);
        }
    }
}
