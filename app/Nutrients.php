<?php

namespace App;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class Nutrients extends Model
{
    protected $fillable=[
        "product_id",
        "glycemic_index",
        "glycemic_load",
        "score",
        "protein",
        "calories",
        "carbs",
        "energy",
        "fats",
        "fiber",
        "sugar",
        "suggestion"

    ];

    public function product(){
       return $this->hasOne(Product::class);
    }

    function toArray(){
        return [
            
            "glycemic_index"=>$this->glycemic_index,
            "glycemic_load"=>$this->glycemic_load,
            "score"=>$this->score,
            "protein"=>$this->protein,
            "calories"=>$this->calories,
            "carbs"=>$this->carbs,
            "energy"=>$this->energy,
            "fats"=>$this->fats,
            "fiber"=>$this->fiber,
            "sugar"=>$this->sugar,
            "suggestion"=>$this->suggestion
        ];
    }
}
