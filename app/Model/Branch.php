<?php

namespace App\Model;

use App\BranchLocation;
use App\Inventory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Branch extends Authenticatable
{
    use Notifiable;

    protected $casts = [

        'coverage' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function inventory(){
        return $this->hasMany(Inventory::class,'branchId');
    }

    public function location(){
        return $this->hasOne(BranchLocation::class,'branch_id');
    }

}
