<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraficZone extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function childrens(){
        return $this->hasMany(\App\Models\TraficZone::class, 'parent', 'id');
    }
}
