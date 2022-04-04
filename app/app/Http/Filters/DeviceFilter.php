<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class DeviceFilter extends AbstractFilter
{
    public const BRAND_ID = 'brand_id';
    public const NAME = 'name';
    public const DEVICE_TYPE_ID = 'device_type_id';
    public const DEVICE_FILTER_ID = 'device_filter_id';
    public const DOPS = 'dops';

    protected function getCallbacks(): array
    {
        return [
            self::BRAND_ID          => [$this, 'brandId'],
            self::NAME              => [$this, 'name'],
            self::DEVICE_TYPE_ID    => [$this, 'deviceTypeId'],
            self::DEVICE_FILTER_ID  => [$this, 'deviceFilterId'],
            self::DOPS              => [$this, 'dops']
        ];
    }

    public function brandId(Builder $builder, $value)
    {
        $builder->leftJoin('device_brands', 'device_brands.device_id', '=', 'devices.id')->where('device_brands.brand_id', $value);
    }

    public function name(Builder $builder, $value)
    {
        $builder>where('devices.name', 'like', "%{$value}%");
    }

    public function deviceTypeId(Builder $builder, $value)
    {
        $builder->where('devices.device_type_id', $value);
    }

    public function deviceFilterId(Builder $builder, $value)
    {
        $builder->where('devices.device_filter_id', $value);
    }

    public function dops(Builder $builder, $value)
    {
        $builder->where('devices.device_type_id', 6);
    }
}
