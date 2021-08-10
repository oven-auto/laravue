<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'device_type_id', 'device_filter_id'];

    public $timestamps = false;

    public function type()
    {
        return $this->hasOne(\App\Models\DeviceType::class, 'id', 'device_type_id')->withDefault();
    }

    public function filter()
    {
        return $this->hasOne(\App\Models\DeviceFilter::class, 'id', 'device_filter_id');
    }

    public function brands()
    {
        return $this->belongsToMany(\App\Models\Brand::class, 'device_brands');
    }

    public function scopeFullData($query)
    {
        return $query->with(['filter', 'type', 'brands']);
    }
}
