<?php

namespace App\Http\Controllers\Api\v1\Front\Device;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Repositories\Device\DeviceRepository;
use App\Http\Resources\Device\DeviceCollection;

class DeviceComplectationController extends Controller
{
    public function get($complectation_id, DeviceRepository $service)
    {
    	$devices = $service->getByComplectationId($complectation_id);
    	return new DeviceCollection($devices);
    }
}
