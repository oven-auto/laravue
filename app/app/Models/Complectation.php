<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;
use App\Models\Traits\PriceChangeable;
use App\Models\Interfaces\SortInterface;
use App\Models\Interfaces\HasPriceInterface;

class Complectation extends Model implements SortInterface, HasPriceInterface
{
    use HasFactory;
    use Filterable;
    use PriceChangeable;

    protected $guarded = [];

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
        return $this->belongsToMany(\App\Models\MarkColor::class, 'complectation_colors', 'complectation_id')->with('color');
    }

    public function colorPacks()
    {
        return $this->belongsToMany(\App\Models\MarkColor::class, 'complectation_color_packs', 'complectation_id')->withPivot('pack_id')->with('color');
    }

    public function markColor()
    {
        return $this->hasMany(\App\Models\MarkColor::class, 'mark_id', 'mark_id')->with('color');
    }

    public function motor()
    {
        return $this->hasOne(\App\Models\Motor::class, 'id', 'motor_id')->withDefault()->with(['transmission', 'driver', 'type']);
    }

    public function brand()
    {
        return $this->hasOne(\App\Models\Brand::class,'id','brand_id')->withDefault();
    }

    public function mark()
    {
        return $this->hasOne(\App\Models\Mark::class,'id','mark_id')->withDefault();
    }

    public function cars()
    {
        return $this->hasMany(\App\Models\Car::class, 'complectation_id', 'id');
    }
}
