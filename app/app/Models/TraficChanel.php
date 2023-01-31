<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraficChanel extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function childrens(){
        return $this->hasMany(\App\Models\TraficChanel::class, 'parent', 'id');
    }
}
