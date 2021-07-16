<?php

namespace App\Model;

use App\Ingredients;
use App\Inventory;
use App\Nutrients;
use App\Recipe;
use App\Tags;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'set_menu' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
        
        
        

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function rating()
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, product_id, count(*) as total'))
            ->groupBy('product_id');
    }

    public function inventory(){
        return $this->hasMany(Inventory::class,'productId');
    }

    public function recipie(){
        return $this->belongsTo(Recipe::class);
    }

    public function ingredients(){
        return $this->belongsToMany(Ingredients::class,'ingredient_product','product_id','ingredient_id');
        
    }

    public function nutrient(){
        return $this->belongsTo(Nutrients::class);
    }

    public function tags(){
        return $this->belongsToMany(Tags::class,'tags_users','product_id','tag_id');
    }
}