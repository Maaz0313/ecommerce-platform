<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $electronicsId = Category::where('name', 'Electronics')->first()->id;
        $clothingId = Category::where('name', 'Clothing')->first()->id;
        $homeKitchenId = Category::where('name', 'Home & Kitchen')->first()->id;
        $booksId = Category::where('name', 'Books')->first()->id;
        $beautyId = Category::where('name', 'Beauty & Personal Care')->first()->id;
        $sportsId = Category::where('name', 'Sports & Outdoors')->first()->id;

        // Products data
        $products = [
            // Electronics
            [
                'name' => 'iPhone 14 Pro Max',
                'description' => 'Apple\'s flagship smartphone with A16 Bionic chip, 6.7-inch Super Retina XDR display, 48MP camera, and 5G connectivity.',
                'price' => 1099.99,
                'stock' => 50,
                'category_id' => $electronicsId,
                'is_active' => true
            ],
            [
                'name' => 'Samsung Galaxy S23 Ultra',
                'description' => 'Samsung\'s premium smartphone with Snapdragon 8 Gen 2 processor, 6.8-inch Dynamic AMOLED display, 200MP camera, and S Pen support.',
                'price' => 1199.99,
                'stock' => 45,
                'category_id' => $electronicsId,
                'is_active' => true
            ],
            [
                'name' => 'MacBook Pro 16-inch',
                'description' => 'Powerful laptop with M2 Pro/Max chip, 16-inch Liquid Retina XDR display, up to 32GB unified memory, and up to 8TB SSD storage.',
                'price' => 2499.99,
                'stock' => 20,
                'category_id' => $electronicsId,
                'is_active' => true
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Wireless noise-canceling headphones with 30-hour battery life, adaptive sound control, and high-resolution audio support.',
                'price' => 399.99,
                'stock' => 30,
                'category_id' => $electronicsId,
                'is_active' => true
            ],
            
            // Clothing
            [
                'name' => 'Men\'s Classic-Fit Chino Pants',
                'description' => 'Comfortable and versatile chino pants made from durable cotton twill with a bit of stretch. Perfect for casual and semi-formal occasions.',
                'price' => 49.99,
                'stock' => 100,
                'category_id' => $clothingId,
                'is_active' => true
            ],
            [
                'name' => 'Women\'s Cashmere Sweater',
                'description' => 'Luxuriously soft 100% cashmere sweater with a classic crew neckline and ribbed trim. Available in multiple colors.',
                'price' => 129.99,
                'stock' => 75,
                'category_id' => $clothingId,
                'is_active' => true
            ],
            [
                'name' => 'Athletic Performance T-Shirt',
                'description' => 'Moisture-wicking, quick-drying performance t-shirt perfect for workouts, running, and other athletic activities.',
                'price' => 34.99,
                'stock' => 150,
                'category_id' => $clothingId,
                'is_active' => true
            ],
            
            // Home & Kitchen
            [
                'name' => 'Nespresso Vertuo Coffee Machine',
                'description' => 'Versatile coffee maker that brews both espresso and coffee with the touch of a button using Nespresso\'s capsule system.',
                'price' => 199.99,
                'stock' => 40,
                'category_id' => $homeKitchenId,
                'is_active' => true
            ],
            [
                'name' => 'KitchenAid Stand Mixer',
                'description' => 'Professional-grade stand mixer with 10 speeds and a powerful motor. Includes a 5-quart stainless steel bowl and various attachments.',
                'price' => 379.99,
                'stock' => 25,
                'category_id' => $homeKitchenId,
                'is_active' => true
            ],
            [
                'name' => 'Egyptian Cotton Bed Sheet Set',
                'description' => 'Luxurious 1000-thread-count Egyptian cotton bed sheets. Set includes 1 flat sheet, 1 fitted sheet, and 2 pillowcases.',
                'price' => 149.99,
                'stock' => 60,
                'category_id' => $homeKitchenId,
                'is_active' => true
            ],
            
            // Books
            [
                'name' => 'Atomic Habits by James Clear',
                'description' => 'A practical guide to building good habits and breaking bad ones. Learn how tiny changes can yield remarkable results.',
                'price' => 24.99,
                'stock' => 200,
                'category_id' => $booksId,
                'is_active' => true
            ],
            [
                'name' => 'The Psychology of Money by Morgan Housel',
                'description' => 'A collection of 19 short stories exploring the strange ways people think about money and how to make better sense of this complicated topic.',
                'price' => 19.99,
                'stock' => 175,
                'category_id' => $booksId,
                'is_active' => true
            ],
            
            // Beauty & Personal Care
            [
                'name' => 'Vitamin C Facial Serum',
                'description' => 'Antioxidant-rich serum that brightens skin tone, reduces fine lines, and promotes collagen production for a more youthful appearance.',
                'price' => 59.99,
                'stock' => 85,
                'category_id' => $beautyId,
                'is_active' => true
            ],
            [
                'name' => 'Premium Electric Toothbrush',
                'description' => 'Advanced sonic technology with multiple cleaning modes, pressure sensor, and built-in timer for optimal oral hygiene.',
                'price' => 149.99,
                'stock' => 50,
                'category_id' => $beautyId,
                'is_active' => true
            ],
            
            // Sports & Outdoors
            [
                'name' => 'Yoga Mat with Alignment Lines',
                'description' => 'Eco-friendly, non-slip yoga mat with alignment markings to help perfect your form during practice. Includes carrying strap.',
                'price' => 79.99,
                'stock' => 100,
                'category_id' => $sportsId,
                'is_active' => true
            ],
            [
                'name' => 'Adjustable Dumbbell Set',
                'description' => 'Space-saving adjustable dumbbells that replace multiple sets. Each dumbbell adjusts from 5 to 52.5 pounds in 2.5-pound increments.',
                'price' => 349.99,
                'stock' => 30,
                'category_id' => $sportsId,
                'is_active' => true
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'category_id' => $product['category_id'],
                'is_active' => $product['is_active']
            ]);
        }
    }
}
