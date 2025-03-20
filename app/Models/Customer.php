<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, HasApiTokens;
    protected $guarded = [];
    protected $casts = [
        'date' => 'datetime',
    ];

    protected $appends = ['image_url'];

    protected function generateImageUrl($imageName)
    {
        if (!empty($imageName)) {
            return asset('uploads/all_photo/' . rawurlencode($imageName));
        } else {
            return null;
        }
    }

    public function getImageUrlAttribute()
    {
        return $this->generateImageUrl($this->image);
    }



}
