<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BranchRequest extends Model
{
    protected $fillable = ['user_id','location_id'];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function location(){
        return $this->belongsTo(BranchLocation::class,'location_id','id');
    }
}
