<?php

namespace App\Repositories\Services;

use App\Models\Service;
use App\Repositories\Services\DTO\ServiceDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Class ServiceRepository
{
    public function getAll(array $data)
    {
        $query = Service::query()->with(['author','prolongation','calculation', 'applicabilities', 'category']);

        $query->leftJoin('service_applicabilities', 'service_applicabilities.service_id', 'services.id');

        if(isset($data['module_id']))
            $query->where('service_applicabilities.module_id', $data['module_id']);

        if(isset($data['category_id']))
            $query->where('services.category_id', $data['category_id']);

        $result = $query->get();

        return $result;
    }



    public function getById(int $id) : Service
    {
        return Service::findOrFail($id);
    }



    private function saveProlongation(Service $service, ServiceDTO $dto) : bool
    {
        $dirty = false;

        if($dto->reminder && $dto->manager_id)
        {
            if($service->prolongation)
            {
                $service->prolongation->fill([
                    'manager_id' => $dto->manager_id,
                    'reminder' => $dto->reminder
                ]);
                if($service->prolongation->isDirty())
                {
                    $service->prolongation->save();
                    $dirty = true;
                }
            } else {
                $dirty = true;
                $service->prolongation()->create([
                    'manager_id' => $dto->manager_id,
                    'reminder' => $dto->reminder
                ]);
            }
        }

        return $dirty;
    }



    private function saveCalculation(Service $service, ServiceDTO $dto) : bool
    {
        $dirty = false;

        if($service->calculation)
        {
            $service->calculation->fill($dto->calculationData());
            if($service->calculation->isDirty())
            {
                $dirty = true;
                $service->calculation->save();
            }
        } else 
        {
            $dirty = true;
            $service->calculation()->create($dto->calculationData());
        }

        return $dirty;
    }



    public function saveApplicability(Service $service, ServiceDTO $dto) : bool
    {
        $service->applicabilities()->delete();

        array_map(function($item) use ($service){
            $service->applicabilities()->create([
                'service_id' => $service->id,
                'applicability' => $item
            ]);
        }, $dto->applicability);

        return 1;
    }



    public function saveOver(Service $service, ServiceDTO $dto) : bool
    {
        $result = (
            $this->saveApplicability($service, $dto) ||
            $this->saveCalculation($service, $dto) ||
            $this->saveProlongation($service, $dto)
        );

        return $result;
    }



    public function create(ServiceDTO $dto)
    {
        $result = DB::transaction(function() use ($dto){
            $service = Service::create(array_merge($dto->mainData(), ['author_id' => Auth::id()]));

            $this->saveOver($service, $dto);

            return $service;
        }, 1);

        return $result;
    }



    public function update(int $id, ServiceDTO $dto)
    {
        $service = $this->getById($id);

        $result = DB::transaction(function() use ($service, $dto){
            $service->fill(array_merge($dto->mainData(), ['author_id' => Auth::id()]));            

            $dirty = $this->saveOver($service, $dto);

            if($service->isDirty() || $dirty)
                $service->save();

            return $service;
        },1);

        return $result;
    }
}