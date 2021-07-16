<?php

namespace App;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class Ingredients extends Model
{
    protected $fillable = [
        
        "name" ,
        "price" ,
        "minimum_integer",
        "image",

    ];

    public function product(){
        return $this->belongsToMany(Product::class,'ingredient_product','ingredient_id','product_id');
    }

    public function toArray()
    {
        return [
            "id"=>$this->id,
            
            "name"=>$this->name,
            "price"=>$this->price,
            "image"=>$this->image,
            "created_at"=>$this->created_at,
            "updated_at"=>$this->updated_at,
            "min_quantity"=>$this->min_quantity,
        ];
    }


}
