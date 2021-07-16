<?php

namespace App;

use App\Model\Branch;
use Illuminate\Database\Eloquent\Model;

class BranchLocation extends Model
{
    protected $fillable = [
        'branch_id',
        'longitude',
        'latitude',
        'city',
        'road',
        'address',
        'pin'
    ];

    public function branch(){
        return $this->belongsTo(Branch::class);
    }
    
    public function toArray()
    {
        $branch = $this->branch;
        $inventory = $branch->inventory;
        return [
            $branch->id=>$inventory
        ];
    }

    public function request(){
        return $this->hasOne(BranchRequest::class,'location_id','id');
    }
}
