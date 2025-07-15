<?php

namespace App\Http\Resources\Worksheet\Service;

use App\Http\Resources\Service\ServiceResource as ServiceServiceResource;
use App\Http\Resources\Services\ServiceCategoryResource;
use App\Http\Resources\User\UserSmallResource;
use App\Http\Resources\Worksheet\Reserve\ReserveList\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'car'                   => $this->car ? [
                'vin'               => $this->car->carable->vin,
                'year'              => $this->car->carable->year,
                'brand'             => $this->car->carable->brand->name,
                'model'             => $this->car->carable->mark->name,
                'type'              => $this->car->getType(),
                'id'                => $this->car->carable->id,
            ] : [],
            'id'                    => $this->id,
            'cost'                  => $this->cost,
            'simple'                => (int) $this->simple,
            'close'                 => (int) $this->close,
            'author'                => new UserSmallResource($this->author),
            'provider'              => new ClientResource($this->provider),
            'payment'               => new ServiceCategoryResource($this->payment),
            'service'               => new ServiceServiceResource($this->service),
            'award'                 => $this->award ? [
                'award'             => $this->award->sum,
                'award_completed'   => $this->award->completed,
            ] : [],
            'contract'              => $this->contract ? [
                'number'            => $this->contract->number,
                'begin_at'          => $this->contract->begin_at ? $this->contract->begin_at->format('d.m.Y') : '',
                'register_at'       => $this->contract->register_at ? $this->contract->register_at->format('d.m.Y') : '',
                'decorator'         => new UserSmallResource($this->contract->decorator),
                'manager'           => new UserSmallResource($this->contract->manager),
            ] : [],
            'deduction'             => $this->deduction->sum ?? '',
            'updated_at'            => $this->updated_at->format('d.m.Y'),   
            'state'                 => $this->state->state,
            'worksheet_id'          => $this->worksheet_id,
            'in_credit'             => $this->hasCredit(),      
        ];
    }
}
