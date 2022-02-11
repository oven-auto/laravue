<?php

namespace App\Http\Controllers\Api\v1\Front\Pack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pack;
use App\Http\Resources\Pack\PackCollection;
use App\Repositories\Pack\PackRepository;

class PackCarController extends Controller
{
	public function get($car_id, PackRepository $service)
	{
		$packs = $service->getByCarId($car_id);
		return new PackCollection($packs);
	}
}