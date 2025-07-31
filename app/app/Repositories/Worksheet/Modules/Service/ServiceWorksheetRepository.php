<?php

namespace App\Repositories\Worksheet\Modules\Service;

use App\Helpers\Array\ArrayHelper;
use App\Http\DTO\Worksheet\Service\CreateServiceDTO;
use App\Http\Filters\WorksheetServiceFilter;
use App\Models\Worksheet\Service\WSMService;
use App\Models\Worksheet\Service\WSMServiceCar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Class ServiceWorksheetRepository
{
    public function getAll(array $data)
    {
        $query = WSMService::query()
            ->select('wsm_services.*')
            ->allRelation();

        $filter = app()->make(WorksheetServiceFilter::class, ['queryParams' => ($data)]);

        $query->filter($filter);

        $result = $query->get();

        return $result;
    }



    public function paginate(array $data)
    {
        $query = WSMService::query()
            ->select('wsm_services.*')
            ->allRelation();
        
        $filter = app()->make(WorksheetServiceFilter::class, ['queryParams' => ($data)]);

        $query->filter($filter);

        $result = $query->simplePaginate(20);

        return $result;
    }



    public function count(array $data)
    {
        $query = WSMService::select([
            DB::raw('COUNT(wsm_services.id) as _count'),
            DB::raw('SUM(wsm_services.cost) as _cost'),
            DB::raw('SUM(wsm_service_awards.sum) as _award'),
        ]);

        $filter = app()->make(WorksheetServiceFilter::class, ['queryParams' => ($data)]);

        $query->filter($filter);

        $res = DB::table($query)->select(
            DB::raw('COUNT(_count) as _count'),
            DB::raw('SUM(_cost) as _cost'),
            DB::raw('SUM(_award) as _award'),
        )->first();
        
        return $res;
    }



    private function createAward(WSMService $service, CreateServiceDTO $dto)
    {
        $service->award()->updateOrCreate(
            ['wsm_service_id' => $service->id],
            ArrayHelper::getOnlyNotNullable((array) $dto->award)
        );
    }



    public function createContract(WSMService $service, CreateServiceDTO $dto)
    {
        $service->contract()->updateOrCreate(
            ['wsm_service_id' => $service->id],
            ArrayHelper::getOnlyNotNullable((array) $dto->contract)
        );
    }



    public function createDeduction(WSMService $service, CreateServiceDTO $dto)
    {
        $service->deduction()->updateOrCreate(
            ['wsm_service_id' => $service->id],
            ArrayHelper::getOnlyNotNullable((array) $dto->deduction)
        );
    }



    public function createCar(WSMService $service, CreateServiceDTO $dto)
    {
        $type = WSMServiceCar::getModelName($dto->car->type);

        $car = $type::findOrFail($dto->car->id);

        if($car) 
        {
            $service->car()->updateOrCreate(
                ['wsm_service_id' => $service->id],
                ['carable_id' => $car->id, 'carable_type' => $type]
            );
        }
    }



    public function create(CreateServiceDTO $dto)
    {
        $result = DB::transaction(function() use($dto) {
            $service = WSMService::create(array_merge((array) $dto->service, ['author_id' => Auth::id()]));

            if(!ArrayHelper::isAllNull((array) $dto->award))
                $this->createAward($service, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->contract))
                $this->createContract($service, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->deduction))
                $this->createDeduction($service, $dto);

            if(!ArrayHelper::isAllNull((array) $dto->car))
                $this->createCar($service, $dto);

            //$service->refresh();

            $service->load(['award', 'contract', 'deduction', 'provider', 'author', 'payment']);

            return $service;
        }, 1);

        return $result;
    }



    public function update(int $id, CreateServiceDTO $dto)
    {
        $result = DB::transaction(function() use($dto, $id) {
            $service = $this->getById($id);

            $service->fill(array_merge((array) $dto->service, ['author_id' => Auth::id()]))->save();
           
            if(!ArrayHelper::isAllNull((array) $dto->award))
                $this->createAward($service, $dto);
            else
                $service->award()->delete();

            if(!ArrayHelper::isAllNull((array) $dto->contract))
                $this->createContract($service, $dto);
            else
                $service->contract()->delete();

            if(!ArrayHelper::isAllNull((array) $dto->deduction))
                $this->createDeduction($service, $dto);
            else
                $service->deduction()->delete();

            $service->load(['award', 'contract', 'deduction', 'provider', 'author', 'payment']);

            return $service;
        }, 1);

        return $result;
    }



    public function getById(int $id)
    {
        return WSMService::findOrFail($id);
    }



    public function delete(int $id)
    {
        $service = $this->getById($id);

        $service->delete();
    }
}