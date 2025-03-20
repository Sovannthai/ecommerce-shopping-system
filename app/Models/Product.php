<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'discount_price',
        'cost_price',
        'stock',
        'category_id',
        'brand_id',
        'is_featured',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'main_image',
        'discount_percentage',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the images for the product.
     */
    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the variants for the product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the ratings/reviews for the product.
     */
    public function ratingsReviews()
    {
        return $this->hasMany(RatingReview::class);
    }

    /**
     * Get the cart items that contain this product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the order items that contain this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function recentlyViewed()
    {
        return $this->hasMany(RecentlyViewed::class);
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_products');
    }

    /**
     * Get the main image of the product.
     */
    public function getMainImageAttribute()
    {
        $primaryImage = $this->productImages()
            ->where('is_primary', true)
            ->first();

        if ($primaryImage) {
            return $primaryImage->image;
        }

        $firstImage = $this->productImages()
            ->first();

        return $firstImage ? $firstImage->image : 'default.jpg';
    }

    public function getPriceWithDiscountAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Get the discount percentage of the product.
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->discount_price || !$this->price || $this->price == 0) {
            return 0;
        }

        return round(100 - (($this->discount_price / $this->price) * 100));
    }
}
