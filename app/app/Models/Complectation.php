<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complectation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function devices()
    {
        return $this->belongsToMany(\App\Models\Device::class, 'complectation_devices', 'complectation_id');
    }

    public function packs()
    {
        return $this->belongsToMany(\App\Models\Pack::class, 'complectation_packs', 'complectation_id');
    }

    public function colors()
    {
        return $this->belongsToMany(\App\Models\MarkColor::class, 'complectation_color', 'complectation_id')->with('color');
    }
}
