<?php

namespace App;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable=[
        "product_id",
        "description",
        "procedure"
    ];

    public function product(){
       return $this->hasOne(Product::class);
    }

    public function toArray()
    {
        return [
            'description'=>$this->description,
            'procedure'=>json_decode($this->procedure)
        ];
    }
}
