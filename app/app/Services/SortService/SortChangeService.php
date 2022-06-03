<?php

namespace App\Services\SortService;
use App\Models\Interfaces\SortInterface;

Class SortChangeService {
    public function changeSort(SortInterface $model, $data = [])
    {
        $activeComplectation = $model::find($data['active']['id']);
        $secondComplectation = $model::find($data['second']['id']);

        $sortOld = $activeComplectation->sort;
        $sortNew = $secondComplectation->sort;

        $activeComplectation->sort = $sortNew;
        $secondComplectation->sort = $sortOld;

        $activeComplectation->save();
        $secondComplectation->save();

        $result = [
            $activeComplectation->id=>$activeComplectation->sort,
            $secondComplectation->id=>$secondComplectation->sort
        ];

        return $result;
    }
}
