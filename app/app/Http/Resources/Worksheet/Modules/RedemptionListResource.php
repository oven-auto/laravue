<?php

namespace App\Http\Resources\Worksheet\Modules;

use Illuminate\Http\Resources\Json\JsonResource;

class RedemptionListResource extends JsonResource
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
            'redemption' => [
                'id' => $this->id,
                'created_at' => $this->created_at->format('d.m.Y (H:i)'),
                'author' => $this->author->cut_name,
                'type' => $this->type->name,
                'sign' => $this->car_sale_sign->name,
                'expectation' => $this->expectation,
                'calculation' => [
                    'price_begin' => $this->last_calculation->price_begin,
                    'price_end' => $this->last_calculation->price_end,
                    'author' => $this->last_calculation->author->cut_name,
                ],
                'offer' => [
                    'price' => $this->last_offer->price,
                    'author' => $this->last_offer->author->cut_name,
                ],
                'control_point' => $this->control_point(),
                'executor' => [
                    'author' => $this->final_author->author->cut_name,
                    'data' => $this->final_author->created_at ? $this->final_author->created_at->format('d.m.Y (H:i)') : '' ,
                ],
            ],
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->full_name,
            ],
            'car' => [
                'id' => $this->client_car->id,
                'year' => $this->client_car->year,
                'mark' => $this->client_car->brand->name,
                'model' => $this->client_car->mark->name,
                'vin' => $this->client_car->vin,
                'register_plate' => $this->client_car->register_plate,
                'odometer' => $this->client_car->odometer,
                'bodywork' => $this->client_car->bodywork->name,
                'color' => [
                    'name' => $this->client_car->color->name,
                    'code' => $this->client_car->color->web,
                ],
                'motor' => [
                    'size' => $this->client_car->engine_size,
                    'power' => $this->client_car->engine_power,
                    'transmission' => $this->client_car->transmission->acronym,
                    'drive' => $this->client_car->drive->acronym,
                ],
            ],

            'worksheet' => [
                'id' => $this->worksheet->id,
                'begin_at' => $this->worksheet->last_action->begin_at->format('d.m.Y (H:i)'),
                'end_at' => $this->worksheet->last_action->end_at->format('d.m.Y (H:i)')
            ],

        ];
    }
}
