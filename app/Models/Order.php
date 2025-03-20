<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_no',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'total_amount',
        'status',
        'payment_status',
        'shipping_method',
        'tracking_number',
        'notes',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the addresses for the order.
     */
    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    /**
     * Get the payments for the order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the shipping address for the order.
     */
    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class)
            ->where('address_type', 'shipping');
    }

    /**
     * Get the billing address for the order.
     */
    public function billingAddress()
    {
        return $this->hasOne(OrderAddress::class)
            ->where('address_type', 'billing');
    }
}
