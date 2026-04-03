<?php

namespace App\Ai\Tools;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CancelOrderTool implements Tool
{

    public function __construct( public User $user){}

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Cancel an existing order by invoice ID for the current user';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $order = Order::where('user_id', $this->user->id)
            ->where('invoice_id', $request['invoice_id'])
            ->first();

        if (!$order) {
            return 'Order not found or does not belong to you.';
        }

        if ($order->status === 'cancelled') {
            return 'Order is already cancelled.';
        }

        $order->update(['status' => 'cancelled']);

        return 'Order ' . $order->invoice_id . ' has been cancelled successfully.';
        

    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'invoice_id' => $schema->string(),
        ];
    }
}
