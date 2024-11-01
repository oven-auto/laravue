<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function appeal()
    {
        return $this->hasOne(\App\Models\Appeal::class,'id','appeal_id')->withDefault();
    }

    public function group()
    {
        return $this->hasOne(\App\Models\ProductGroup::class,'id', 'group_id')->withDefault();
    }
}
