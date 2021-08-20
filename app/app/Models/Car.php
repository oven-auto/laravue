<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function brand()
    {
        return $this->hasOne(\App\Models\Brand::class, 'id', 'brand_id')->withDefault();
    }

    public function mark()
    {
        return $this->hasOne(\App\Models\Mark::class, 'id', 'mark_id')->withDefault();
    }

    public function complectation()
    {
        return $this->hasOne(\App\Models\Complectation::class, 'id', 'complectation_id')->withDefault()->with('motor');
    }

    public function color()
    {
        return $this->hasOne(\App\Models\MarkColor::class, 'id', 'mark_color_id')->withDefault()->with('color');
    }

    public function packs()
    {
        return $this->belongsToMany(\App\Models\Pack::class, 'car_packs', 'car_id');
    }

    public function devices()
    {
        return $this->belongsToMany(\App\Models\Device::class, 'car_devices', 'car_id');
    }
}
