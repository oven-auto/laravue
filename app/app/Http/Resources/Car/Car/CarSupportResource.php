<?php

namespace App\Http\Resources\Car\Car;

use App\Helpers\String\StringHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class CarSupportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $support = [
            'complectation'             => new CarComplectationResource($this),

            'option_price' => $this->option_price->price ?? 0,

            'status' => $this->statusAll,

            'full_price' => $this->full_price->price ?? 0,

            'over_price'                => $this->over_price->price ?? 0,

            'brand' => $this->brand->name,

            'mark' => $this->mark->name,

            'car_author' => [
                'author'                    => $this->author->cut_name,
                'created_at'                => $this->created_at->format('d.m.Y (H:i)'),
            ],

            'purchase_author' => [
                'author' => $this->purchase->author->cut_name ?? '',
                'created_at' => $this->purchase ? $this->purchase->created_at->format('d.m.Y (H:i)') : '',
            ],

            'provider' => [
                'provider_name' => $this->provider->cutName,
                'provider_id' => $this->provider->provider_id
            ],

            'order_type' => [
                'author' => $this->order_type->author->cut_name ?? '',
                'created_at' => $this->order_type ? $this->order_type->created_at->format('d.m.Y (H:i)') : '',
            ],

            'collector' => [
                'author' => $this->collector->author->cut_name ?? '',
                'created_at' => $this->collector ? $this->collector->updated_at->format('d.m.Y (H:i)') : '',
            ],

            'delivery_terms' => [
                'created_at' => $this->delivery_terms->updated_at ? $this->delivery_terms->updated_at->format('d.m.Y (H:i)') : '',
                'author' => $this->delivery_terms->author->cut_name ?? '',
            ],

            'coast' => [
                'base'      => $this->complectationPrice(),
                'option'    => $this->optionPrice(),
                'over'      => $this->overPrice(),
                'tuning'    => $this->tuningPrice(),
                'full'      => $this->fullPrice(),
                'sale'      => $this->sale(),
            ],
        ];

        $support = array_merge($support, $this->getLogisticAuthors()->collapse()->toArray());

        return $support;
    }
}
