<?php

namespace App;

use App\Model\Branch;
use App\Model\Product;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'branchId',
        'productId',
        'count'
    ];

    public function branch(){
        return $this->belongsTo(Branch::class,'branchId','id');
    }

    public function product(){
        return $this->belongsTo(Product::class,'productId','id');
    }

    public function incrementCount($num=1){
        $this->count+=$num;
        $this->save();
    }

    public function decrementCount($num=1){
        $this->count-=$num;
        $this->save();
    }

    public function toArray()
    {
        return [
            'item'=>$this->product->toArray(),
            'branch'=>$this->branch->toArray(),
            'quantity'=>$this->count
        ];
    }
}
