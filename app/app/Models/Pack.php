<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function brand()
    {
        return $this->hasOne(\App\Models\Brand::class, 'id', 'brand_id')->withDefault();
    }

    public function devices()
    {
        return $this->belongsToMany(\App\Models\Device::class, 'pack_devices');
    }

    public function scopeFullData($query)
    {
        return $query->with(['brand', 'devices']);
    }
}
