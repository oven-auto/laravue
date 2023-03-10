<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appeal extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function trafic_products()
    {
        return $this->hasMany(\App\Models\TraficProduct::class, 'appeal_id', 'id')->orderBy('group_id')->orderBy('name');
    }

    public function trafic_appeals()
    {

    }
}
