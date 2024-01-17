<?php

namespace App\Http\Resources\Worksheet\Modules;

use Illuminate\Http\Resources\Json\JsonResource;

class RedemptionResource extends JsonResource
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
            'author' => $this->author->cut_name,
            'car' => [
                'car_id' => $this->client_car_id,
                'brand' => $this->client_car->brand->name,
                'mark' => $this->client_car->mark->name,
                'year' => $this->client_car->year,
                'odometer' => $this->client_car->odometer,
                'register_plate' => $this->client_car->register_plate,
                'vin' => $this->client_car->vin,
            ],
            'car_sale_sign_id' => $this->car_sale_sign_id,
            'redemption_type_id' => $this->redemption_type_id,
            'expectation' => $this->expectation,
            'last_editor' => $this->lastAuthor(),
            'status' => $this->control_point()->title,
            'created_at' => $this->created_at->format('d.m.Y (H:i)'),
            'calculations' => $this->calculations->map(function($calc){
                return [
                    'author' => $calc->author->cut_name,
                    'created_at' => $calc->created_at->format('d.m.Y (H:i)'),
                    'price_begin' => $calc->price_begin,
                    'price_end' => $calc->price_end,
                ];
            }),
            'purchases' => $this->purchases->map(function($purchase){
                return [
                    'author' => $purchase->author->cut_name,
                    'created_at' => $purchase->created_at->format('d.m.Y (H:i)'),
                    'price' => $purchase->price,
                ];
            }),
            'offers' => $this->offers->map(function($offer){
                return [
                    'author' => $offer->author->cut_name,
                    'created_at' => $offer->created_at->format('d.m.Y (H:i)'),
                    'price' => $offer->price,
                ];
            }),
            'links' => $this->links->map(function($link){
                return [
                    'author' => $link->author->cut_name,
                    'created_at' => $link->created_at->format('d.m.Y (H:i)'),
                    'text' => $link->url,
                    'icon' => $link->icon,
                    'id' => $link->id
                ];
            }),
        ];
    }
}
