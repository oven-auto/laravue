<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplectationModerator extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(\App\Models\User::class,'id','user_id')->withDefault();
    }

    public function complectation()
    {
        return $this->hasOne(\App\Models\Complectation::class,'id','complectation_id');
    }
}
