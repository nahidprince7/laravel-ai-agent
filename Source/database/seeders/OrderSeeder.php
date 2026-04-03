<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 8 orders with random products
        for ($i = 0; $i < 8; $i++) {
            // Get random products for the order (2-3 products)
            $products = Product::inRandomOrder()->take(rand(2, 3))->get();

            $items = [];
            $totalAmount = 0;

            foreach ($products as $product) {
                $quantity = rand(1, 3);
                $items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                ];
                $totalAmount += $product->price * $quantity;
            }

            Order::create([
                'items' => $items,
                'total_amount' => $totalAmount,
                'status' => ['pending', 'completed', 'cancelled'][rand(0, 2)],
            ]);
        }
    }
}
