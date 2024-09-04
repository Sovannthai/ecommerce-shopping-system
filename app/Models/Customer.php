<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'date' => 'datetime',
    ];

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }
}
