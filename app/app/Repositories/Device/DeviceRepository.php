<?php

namespace App\Repositories\Device;

use App\Models\Device;

Class DeviceRepository
{
	public function getByComplectationId($complect_id)
	{
		$devices = Device::leftJoin('complectation_devices', 'complectation_devices.device_id', '=', 'devices.id')
    		->where('complectation_devices.complectation_id', $complect_id)
    		->get();

    	return $devices;
	}
}