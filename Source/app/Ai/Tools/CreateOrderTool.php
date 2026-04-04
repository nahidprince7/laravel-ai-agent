<?php

namespace App\Ai\Tools;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CreateOrderTool implements Tool
{
    public function __construct(public User $user){}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
         return 'Create a new order for the current user by product name';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
       $product = Product::where('name', 'like', '%' . $request['product_name'] . '%')->first();

        if (!$product) {
            return 'Product not found.';
        }

        $order = Order::create([
            'user_id'    => $this->user->id,
            'product_id' => $product->id,
            'qty'        => $request['quantity'] ?? 1,  // ← change quantity to qty
            'status'     => 'ordered',
        ]);

        return 'Order created successfully. Invoice ID: ' . $order->invoice_id;
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        // For gemini
        // return [
        //     'product_name' => $schema->string(),
        //     'quantity'     => $schema->integer(),
        // ];

        return [
            'product_name' => $schema->string()->description('The name of the product to order'),
            'quantity' => $schema->integer()->description('The quantity to order, default is 1'),
        ];
    }
}
