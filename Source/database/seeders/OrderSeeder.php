<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $statuses = ['ordered', 'cancelled', 'completed'];

        for ($i = 0; $i < 30; $i++) {
            $user = $users->random();
            $product = $products->random();
            $qty = rand(1, 5);

            Order::create([
                'invoice_id' => 'INV-' . strtoupper(uniqid()),
                'user_id' => $user->id,
                'product_id' => $product->id,
                'qty' => $qty,
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}
