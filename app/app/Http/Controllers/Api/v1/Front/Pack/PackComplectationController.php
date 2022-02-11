<?php

namespace App\Http\Controllers\Api\v1\Front\Pack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pack;
use App\Http\Resources\Pack\PackCollection;
use App\Repositories\Pack\PackRepository;

class PackComplectationController extends Controller
{
	public function get($complect_id, PackRepository $service)
	{
		$packs = $service->getByComplectationId($complect_id);
		return new PackCollection($packs);
	}
}