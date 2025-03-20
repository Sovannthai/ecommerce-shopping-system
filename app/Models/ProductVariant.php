<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price_adjustment',
        'stock',
    ];

    /**
     * Get the product that owns the variant.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attributes for the variant.
     */
    public function attributes()
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }

    /**
     * Get the cart items for the variant.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the order items for the variant.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
