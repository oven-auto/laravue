<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;
use App\Models\Traits\Createable;

class Device extends Model
{
    use HasFactory, Filterable, Createable;

    protected $fillable = ['name', 'device_type_id', 'device_filter_id', 'tuning', 'install_target'];

    public $timestamps = false;

    public function type()
    {
        return $this->hasOne(\App\Models\DeviceType::class, 'id', 'device_type_id')->withDefault();
    }

    public function filter()
    {
        return $this->hasOne(\App\Models\DeviceFilter::class, 'id', 'device_filter_id')->withDefault();
    }

    public function brands()
    {
        return $this->belongsToMany(\App\Models\Brand::class, 'device_brands');
    }

    public function scopeFullData($query)
    {
        return $query->with(['filter', 'type', 'brands']);
    }

    public function image()
    {
        return $this->hasOne(\App\Models\DeviceImage::class)->withDefault();
    }

    public function scopeForFilter($query)
    {
        return $query->select(['devices.*','device_types.sort'])
            ->fullData()
            ->withCount('image')
            ->leftJoin('device_types', 'device_types.id', 'devices.device_type_id')
            ->orderBy('device_types.sort')
            ->orderBy('devices.name');
    }
}
