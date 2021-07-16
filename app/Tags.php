<?php

namespace App;

use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    protected $fillable = ['key'];

    public function products(){
        return $this->belongsToMany(Product::class,'tags_users','tag_id','product_id');
    }

    

}
