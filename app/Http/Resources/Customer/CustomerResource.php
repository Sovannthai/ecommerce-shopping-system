<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'fname'  => $this->fname,
            'lname'  => $this->lname,
            'phone'  => $this->phone,
            'email'  => $this->email,
            'status' => $this->status,
            'image'  => $this->image_url,
        ];
    }
}
