<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagsUser extends Model
{
    protected $fillable = [
        'product_id',
        'tag_id'
    ];
}
