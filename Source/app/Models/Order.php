<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'product_id',
        'qty',
        'status',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->invoice_id = 'INV-' . strtoupper(uniqid());
        });
    }
}
