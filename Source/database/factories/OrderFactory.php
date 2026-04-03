<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random products for the order
        $products = Product::inRandomOrder()->take($this->faker->numberBetween(2, 3))->get();

        $items = [];
        $totalAmount = 0;

        foreach ($products as $product) {
            $quantity = $this->faker->numberBetween(1, 3);
            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
            $totalAmount += $product->price * $quantity;
        }

        return [
            'items' => $items,
            'total_amount' => $totalAmount,
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}
