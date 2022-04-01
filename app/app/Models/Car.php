<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    public function price()
    {
        return $this->hasOne(\App\Models\CarPrice::class, 'car_id', 'id')->withDefault();
    }

    public function equipments()
    {
        return $this->belongsToMany(\App\Models\Device::class, 'car_equipments', 'car_id');
    }

    public function fixedprice()
    {
        return $this->hasOne(\App\Models\CarFixedPrice::class,'car_id','id')->withDefault();
    }
}
