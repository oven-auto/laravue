<?php

namespace App\Repositories\Pack;

use App\Models\Pack;

Class PackRepository
{
	public function getByCarId($car_id)
	{
		$packs = Pack::select('packs.*')
			->ByCarId($car_id)
			->with('devices')
			->get();

		return $packs;
	}

	public function getByComplectationId($complectation_id)
	{
		$packs = Pack::select('packs.*')
			->ByComplectId($complectation_id)
			->with('devices')
			->get();

		return $packs;
	}
}