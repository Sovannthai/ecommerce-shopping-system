<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingReview extends Model
{
    use HasFactory;

    protected $table = 'ratings_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'review',
        'status',
    ];

    /**
     * Get the product that owns the rating/review.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that owns the rating/review.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
