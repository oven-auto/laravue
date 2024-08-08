<?php

namespace App\Http\Resources\Car\Car;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Worksheet\Reserve\ReserveList\CarResource;

class CarListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'state' => [
                'title' => $this->getReserveStatus(),
                'color' => $this->getReserveStatusColor(),
            ],

            'car' => new CarResource($this),

            'worksheet' => [
                'id' => $this->getWorksheetId(),
            ],

            'cost' => [
                'status'    => [
                    'state' => (int)$this->isFixed(),
                ],
                'base'                  => $this->getComplectationPrice(),
                'option'                => $this->getOptionPrice(),
                'over'                  => $this->getOverPrice(),
                'tuning'                => $this->getTuningPrice(),
                'full'                  => $this->getFullPrice(),
                'contract_cost'         => $this->getFullPrice(),
                'sale_sum'              => $this->getReserveSale(),
                'total_cost'            => $this->getTotalPrice(),
            ],

            'purchase' => [
                'ransom_date'           => $this->getRansomDate(),
                'purchase'              => $this->purchase->cost ?? 0,
                'detailing_cost'       => ($this->purchase->cost ?? 0) + ($this->detailing_costs->sum('price') ?? 0),
            ],

            'delivery' => [
                'provider'              => $this->provider->cut_name,
                'delivery_term'         => [
                    'term_name' => $this->delivery_terms->term->name ?? '',
                    'deposit_name' => $this->collector->collector->name ?? ''
                ],
                'order_type'            => $this->order_type ? $this->order_type->type->name : 'Тип заказа не указан',
            ],

            'report' => [
                'off_date' => $this->getStateByName('off_date'),
                'off_invalid' => $this->disable_off,
                'type' => [
                    'status' => $this->getReportTypeStatus(),
                    'title' => $this->getReportTypeString(),
                ],
            ],
        ];
    }
}
