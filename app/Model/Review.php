<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $casts = [
        'product_id' => 'integer',
       
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function toArray()
    {
        return [
        "id" => $this->id,
        "product_id" => $this->product_id,
        "user_id" => $this->customer,
        "comment" => $this->comment,
        "attachment" => $this->attachment,
        "rating" => $this->rating,
        "created_at" => $this->created_at,
        "updated_at" => $this->updated_at,
        
        ];
    }
}
