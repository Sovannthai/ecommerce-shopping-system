<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'attribute_value_id',
    ];

    /**
     * Get the product variant that owns the attribute.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the attribute value that belongs to the variant attribute.
     */
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
