<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            'Electronics' => [
                ['name' => 'iPhone 14', 'price' => 999.99, 'stock' => 50, 'description' => 'Latest Apple smartphone with A15 Bionic chip'],
                ['name' => 'MacBook Pro', 'price' => 1999.99, 'stock' => 25, 'description' => 'Powerful laptop for professionals with M2 chip'],
                ['name' => 'Sony Headphones', 'price' => 349.99, 'stock' => 100, 'description' => 'Premium noise-cancelling wireless headphones'],
                ['name' => 'Samsung Galaxy S23', 'price' => 899.99, 'stock' => 40, 'description' => 'Flagship Android smartphone with advanced camera'],
                ['name' => 'iPad Air', 'price' => 599.99, 'stock' => 35, 'description' => 'Versatile tablet for work and entertainment'],
            ],
            'Clothing' => [
                ['name' => 'Classic T-Shirt', 'price' => 29.99, 'stock' => 200, 'description' => 'Comfortable cotton t-shirt in various colors'],
                ['name' => 'Slim Fit Jeans', 'price' => 79.99, 'stock' => 150, 'description' => 'Modern slim fit denim jeans'],
                ['name' => 'Winter Jacket', 'price' => 149.99, 'stock' => 75, 'description' => 'Warm and stylish winter jacket'],
                ['name' => 'Casual Sneakers', 'price' => 89.99, 'stock' => 120, 'description' => 'Comfortable everyday sneakers'],
            ],
            'Books' => [
                ['name' => 'Laravel Guide', 'price' => 49.99, 'stock' => 80, 'description' => 'Comprehensive guide to Laravel framework'],
                ['name' => 'JavaScript Guide', 'price' => 39.99, 'stock' => 90, 'description' => 'Modern JavaScript development guide'],
                ['name' => 'PHP Best Practices', 'price' => 44.99, 'stock' => 70, 'description' => 'Best practices for PHP development'],
            ],
        ];

        foreach ($products as $categoryName => $categoryProducts) {
            $category = Category::where('name', $categoryName)->first();

            foreach ($categoryProducts as $product) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']),
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'description' => $product['description'],
                ]);
            }
        }
    }
}
