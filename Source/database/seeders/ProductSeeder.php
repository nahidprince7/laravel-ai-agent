<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('slug', 'electronics')->first();

        if ($electronics) {
            Product::create([
                'category_id' => $electronics->id,
                'name' => 'Smartphone Pro',
                'price' => 999.99,
                'stock' => 10,
                'description' => 'A high-end smartphone with advanced features.'
            ]);
        }
    }
}