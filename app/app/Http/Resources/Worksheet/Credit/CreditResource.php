<?php

namespace App\Http\Resources\Worksheet\Credit;

use App\Http\Resources\User\UserSmallResource;
use App\Http\Resources\Worksheet\Reserve\ReserveList\ClientResource;
use App\Http\Resources\Worksheet\Service\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditResource extends JsonResource
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
                'id'                => $this->car->id,
            ] : [],
            'id'                    => $this->id,
            'debtor'                => new ClientResource($this->debtor),
            'creditor'              => new ClientResource($this->creditor),
            'tactic'                => $this->tactic ? [
                'id' => $this->tactic->id,
                'name' => $this->tactic->name,
            ] : [],
            'worksheet_id' => $this->worksheet_id,

            'period' => $this->calculation->period ?? 0,
            'cost' => $this->calculation->cost ?? 0,
            'first_pay' => $this->calculation->first_pay ?? 0,
            'month_pay' => $this->calculation->month_pay ?? 0,
            'simple' => $this->calculation->simple ?? 0,

            'status' => $this->status ? [
                'id' => $this->status->id,
                'name' => $this->status->name,
            ] : [],
            'author' => new UserSmallResource($this->author),

            //ПРИМЕРНОЕ НАПОЛНЕНИЕ
            'approximates' => $this->approximates->map(function($item){
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                ];
            }),

            //CONTRACT
            'register_at' => $this->contract ? ($this->contract->register_at->format('d.m.Y') ?? '') : '',
            
            'decorator' => $this->contract ? new UserSmallResource($this->contract->decorator) : [],

            'services' => ServiceResource::collection($this->services),

            'award' => $this->award->sum ?? 0,
            'award_complete' => $this->award->completed ?? 0,

            'close' => (int) $this->close,

            'deduction' => $this->deduction->sum ?? 0,

            'updated_at' => $this->updated_at->format('d.m.Y'),

            'state'                => $this->state->state,   
        ];
    }
}
