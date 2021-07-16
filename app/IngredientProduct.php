<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IngredientProduct extends Model
{

    protected $table='ingredient_product';
    protected $fillable = [
        'product_id','ingredient_id'
    ];
}
