<?php

namespace App\Http\Resources\Car\Car;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\String\StringHelper;

class CarListResource extends JsonResource
{
    private function purchase($purchaseCost, $detailingFullPrice)
    {
        return join(' ', [
            'Закуп',
            StringHelper::moneyMask($purchaseCost),
            StringHelper::strWrap(StringHelper::moneyMask(value: $detailingFullPrice, sign: true))
        ]);
    }



    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'state'                 => [
                'title' => $this->getCurrentState(),
                'color' => $this->getCurrentStateColor(),
            ],

            'id'                    => $this->id,
            'year'                  => $this->year,
            'brand'                 => $this->brand->name,
            'mark'                  => $this->mark->name,

            'complectation_code'    => $this->complectation->code,
            'complectation_id'      => $this->complectation->id,
            'complectation'         => $this->complectation->name,
            'motor_size'            => $this->complectation->motor->size,
            'motor_power'           => $this->complectation->motor->power,
            'motor_transmission'    => $this->complectation->motor->transmission->acronym,
            'motor_driver'          => $this->complectation->motor->driver->acronym,
            'body'                  => $this->complectation->bodywork->name,
            'vehicle'               => $this->complectation->vehicle->name,

            'color'                 => [
                'name' => $this->color->name ?? '',
                'code' => $this->color->base->web ?? ''
            ],

            'logistic_status'       => $this->currentCarState(),

            'vin'                   => $this->vin,
            'order_number'          => $this->order ? $this->order->order_number : '',
            'marker'                => $this->marker->marker ?? [], //->name ?? 'Без контрмарки',

            'ransom_date'           => $this->getLogisticDates('ransom_date'),
            'purchase'              => StringHelper::moneyMask($this->purchase->cost ?? 0),
            'detailing_coast'       => StringHelper::moneyMask($this->detailing_costs->sum('price') ?? 0),


            'provider'              => $this->provider->cut_name,
            'delivery_term'         => ($this->delivery_terms->term->name ?? '') . StringHelper::strWrap($this->collector->collector->name ?? ''),
            'order_type'            => $this->order_type ? $this->order_type->type->name : 'Тип заказа не указан',

            'price' => [
                'status'          => $this->isFixedPrice() ? 'Зафиксировано договором' : '',
                'base'      => 'Кузов '
                    . StringHelper::moneyMask($this->complectationPrice())
                    . '(' . $this->getBodyDatePrice() . ')',
                'option'    => 'Опции ' . StringHelper::moneyMask($this->optionPrice()),
                'over'      => 'Переоценка ' . StringHelper::moneyMask($this->overPrice()),
                'tuning'    => 'Тюнинг ' . StringHelper::moneyMask($this->tuningPrice()),
                'full'      => 'Прайс-лист ' . StringHelper::moneyMask($this->fullPrice()),
            ],

            'off_date' => [
                'date' => $this->getLogisticDates('off_date'),
                'invalid' => $this->disable_off,
            ],

            'pts'               => $this->hasPTS(),

            'deal' => $this->reserve ? [
                'tradeins' => [
                    'contract' => $this->getReserveTradeins()->map(function ($item) {
                        return [
                            'year'          => $item->year,
                            'brand'         => $item->brand->name,
                            'mark'          => $item->mark->name,
                            'odometer'      => $item->odometer,
                            'vin'           => $item->vin,
                            'created_at'    => $item->created_at->format('d.m.Y'),
                        ];
                    }),
                    'redemption' => $this->getWorksheetTradeins()->map(function ($item) {
                        return [
                            'year'          => $item->client_car->year,
                            'brand'         => $item->client_car->brand->name,
                            'mark'          => $item->client_car->mark->name,
                            'odometer'      => $item->client_car->odometer,
                            'vin'           => $item->client_car->vin,
                            'created_at'    => $item->created_at->format('d.m.Y'),
                        ];
                    }),
                ],
                'client' => [
                    'id'                => $this->reserve->worksheet->client->id,
                    'name'              => $this->reserve->worksheet->client->full_name,
                    'phone'             => $this->reserve->worksheet->client->phones->first()->phone ?? '',
                    'location'          => $this->reserve->worksheet->client->zone->name,
                ],
                'coast' => [
                    'car'               => 'Цена ' .        StringHelper::moneyMask($this->reserve->getCarCoast()),
                    'sale'              => 'Скидка ' .      StringHelper::moneyMask($this->reserve->getCarSale()),
                    'total'             => 'Итого ' .       StringHelper::moneyMask($this->reserve->getCarFullCoast()),
                ],
                'worksheet' => [
                    'id' => $this->reserve->worksheet->id,
                ],
                'contract' => $this->reserve->contract ? [
                    'pdkp'         => [
                        'offer_date' => $this->reserve->contract->pdkpOfferDate,
                        'decorator' => $this->reserve->contract->pdkp_decorator->cut_name,
                    ],
                    'dkp'          => [
                        'offer_date' => $this->reserve->contract->dkpOfferDate,
                        'decorator' => $this->reserve->contract->dkp_decorator->cut_name,
                    ],
                    'payment' => $this->reserve->payments->count() ? [
                        'amount'    => StringHelper::moneyMask($this->reserve->getTotalPayments()),
                        'balance'   => StringHelper::moneyMask($this->reserve->getBalance()),
                    ] : [],
                    'sale_date' => [
                        'date' => $this->reserve->sale->date_at ? $this->reserve->sale->date_at->format('d.m.Y') : '',
                        'invalid' => $this->disable_sale,
                    ],
                    'issue_date' => $this->reserve->issue->date_at ? $this->reserve->issue->date_at->format('d.m.Y') : '',
                ] : [],
            ] : [],
        ];
    }
}
