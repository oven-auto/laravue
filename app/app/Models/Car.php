<?php

namespace App\Models;

use App\Helpers\String\StringHelper;
use App\Repositories\Car\Car\DTO\CarTuningDTO;
use App\Repositories\Car\Car\DTO\LogisticDateDTO;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Filterable;
use stdClass;

/**
 * CAR MODEL
 *
 * @method option_price         relation hasOne
 * @method author               relation hasOne
 * @method brand                relation hasOne
 * @method mark                 relation hasOne
 * @method complectation        relation hasOne
 * @method color                relation hasOne
 * @method order                relation hasOne
 * @method provider             relation hasOne
 * @method marker               relation hasOne
 * @method trade_marker         relation hasOne
 * @method order_type           relation hasOne
 * @method logistic_dates       relation hasMany
 * @method technic              relation hasOne
 * @method audio                relation hasOne
 * @method purchase             relation hasOne
 * @method delivery_terms       relation hasOne
 * @method options              relation belongsToMany
 * @method detailing_costs      relation hasMany
 * @method tuning               relation belongsToMany
 * @method tuning_price         relation hasOne
 * @method over_price           relation hasOne
 * @method full_price           relation hasOne
 * @method state_status         relation hasOne
 * @method comment              relation hasOne
 * @method collector            relation hasOne
 * @method reserve               relation hasOne
 *
 * SAVE
 *
 * @method saveOrderType        @param int|null $orderTypeId
 * @method saveOrderNumber      @param string|null $orderNumber
 * @method saveMarker           @param int|null $markerId
 * @method saveTradeMarker      @param int|null $tradeMarkerId
 * @method saveProvider         @param int|null $providerId
 * @method saveLogisticDates    @param LogisticDateDTO $dto
 * @method saveTechnic          @param int|null $technicId
 * @method saveAudio            @param string|null $audioCode
 * @method savePurchase         @param int|null $cost
 * @method saveDeliveryTerm     @param array|null $deliveryTerms
 * @method saveOptions          @param array|null $optionArray
 * @method saveDetailingCost    @param array|null $detailingArray
 * @method saveTuning           @param CatTuningDTO|null $tuningArray
 * @method saveOverPrice        @param int|null $overPrice
 * @method saveComment          @param string $comment
 * @method saveCollector        @param int $collector_id
 *
 * ATTRIBUTES
 *
 * @method getStatusAttribute
 *
 * GET
 *
 * @method getLogisticDates     @param string|null $key @param string $format
 * @method getLogisticAuthors   @param string|null $key @param srring $format
 */



class Car extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $guarded = [];

    /**
     * SCOPES
     */

    /* RELATIONS */
    public function state_status()
    {
        return $this->hasOne(\App\Models\CarState::class, 'status', 'status');
    }

    /**
     * FULL PRICE
     */
    public function full_price()
    {
        return $this->hasOne(\App\Models\CarFullPrice::class, 'car_id', 'id');
    }



    /**
     * TUNING
     */
    public function tuning()
    {
        return $this->belongsToMany(\App\Models\Tuning::class, 'car_tunings', 'car_id');
    }



    /**
     * TUNING PRICE
     */
    public function tuning_price()
    {
        return $this->hasOne(\App\Models\CarTuningPrice::class, 'car_id', 'id')->withDefault();
    }



    /**
     * OVER PRICE
     */
    public function over_price()
    {
        return $this->hasOne(\App\Models\CarOverPrice::class, 'car_id', 'id')->withDefault();
    }



    /**
     *
     */
    public function option_price()
    {
        return $this->hasOne(\App\Models\CarOptionPrice::class, 'car_id', 'id')->withDefault();
    }



    /**
     * AUTHOR
     */
    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    /**
     * BRAND
     */
    public function brand()
    {
        return $this->hasOne(\App\Models\Brand::class, 'id', 'brand_id');
    }



    /**
     * MODEL
     */
    public function mark()
    {
        return $this->hasOne(\App\Models\Mark::class, 'id', 'mark_id');
    }



    /**
     * COMPLECTATION
     */
    public function complectation()
    {
        return $this->hasOne(\App\Models\Complectation::class, 'id', 'complectation_id')->withTrashed();
    }



    /**
     * COLOR
     */
    public function color()
    {
        return $this->hasOne(\App\Models\DealerColor::class, 'id', 'color_id');
    }



    /**
     * ORDER NUMBER
     */
    public function order()
    {
        return $this->hasOne(\App\Models\CarOrder::class, 'car_id', 'id');
    }



    /**
     * PROVIDER
     */
    public function provider()
    {
        return $this->hasOne(\App\Models\CarProvider::class, 'car_id', 'id');
    }



    /**
     * MARKER
     */
    public function marker()
    {
        return $this->hasOne(\App\Models\CarMarker::class, 'car_id', 'id');
    }



    /**
     * TRADE MARKER
     */
    public function trade_marker()
    {
        return $this->hasOne(\App\Models\CarTradeMarker::class, 'car_id', 'id');
    }



    /**
     * ORDER_TYPE
     */
    public function order_type()
    {
        return $this->hasOne(\App\Models\CarOrderType::class, 'car_id', 'id');
    }



    /**
     * LOGISTIC DATES
     */
    public function logistic_dates()
    {
        return $this->hasMany(\App\Models\CarDateLogistic::class, 'car_id', 'id');
    }



    /**
     * TECHNIC
     */
    public function technic()
    {
        return $this->hasOne(\App\Models\CarTechnic::class, 'car_id', 'id');
    }



    /**
     * AUDIO
     */
    public function audio()
    {
        return $this->hasOne(\App\Models\CarAudio::class, 'car_id', 'id');
    }



    /**
     * PURCHASE
     */
    public function purchase()
    {
        return $this->hasOne(\App\Models\CarPurchase::class, 'car_id', 'id');
    }



    /**
     * CAR DELIVERY TERM
     */
    public function delivery_terms()
    {
        return $this->hasOne(\App\Models\CarDeliveryTerm::class, 'car_id', 'id')->withDefault();
    }



    /**
     * CAR OPTIONS
     */
    public function options()
    {
        return $this->belongsToMany(\App\Models\Option::class, 'car_options', 'car_id');
    }



    /**
     * CAR DETAILING COST
     */
    public function detailing_costs()
    {
        return $this->hasMany(\App\Models\CarDetailingCost::class, 'car_id', 'id');
    }



    /**
     * COMMENT
     */
    public function comment()
    {
        return $this->hasOne(\App\Models\CarComment::class, 'car_id', 'id');
    }



    /**
     * COLLECTOR
     */
    public function collector()
    {
        return $this->hasOne(\App\Models\CarCollector::class, 'car_id', 'id');
    }



    /**
     * RESERVE
     */
    public function reserve()
    {
        return $this->hasOne(\App\Models\WsmReserveNewCar::class, 'car_id', 'id');
    }



    /*************************************************** */
    /*************************************************** */
    /**ATTRIBUTES*************************************** */
    /*************************************************** */
    /*************************************************** */



    /**
     * CAR STATUS
     */
    public function getStatusAllAttribute()
    {
        $state = $this->logistic_dates->where('state.state', $this->logistic_dates->max('state.state'))->first();

        $state_status = $this->state_status;

        if ($state_status)
            return join(' ', [
                $state_status->description ?? 'Заявка',
                $state ? $state->updated_at->format('d.m.Y') : '',
                $state ? '(' . $state->author->cut_name . ')' : ''
            ]);
    }



    /**
     * CAR STATUS WITHOUT AUTHOR
     */
    public function getStatusWithoutAuthorAttribute()
    {
        $state = $this->logistic_dates->where('state.state', $this->logistic_dates->max('state.state'))->first();

        $state_status = $this->state_status;

        return join(' ', [
            $state_status->description ?? 'Заявка',
            isset($state->updated_at) ? $state->updated_at->format('d.m.Y') : $this->created_at->format('d.m.Y (H:i)'),
        ]);
    }






    /*************************************************** */
    /*************************************************** */
    /**SAVE METHODS **************************************/
    /*****************************************************/
    /*****************************************************/



    public function saveCollector(int $collector_id = null)
    {
        if (!$collector_id)
            return;

        $collectorCurrent = $this->collector->collector_id ?? null;

        if ($collector_id && $collectorCurrent != $collector_id)
            $this->collector()->updateOrCreate(
                ['car_id' => $this->id],
                [
                    'collector_id' => $collector_id,
                    'author_id' => auth()->user()->id
                ]
            );
    }



    /**
     * SAVE ORDER TYPE
     */
    public function saveOrderType(int $orderTypeId = null)
    {
        $currentId = $this->order_type->order_type_id ?? null;

        if ($orderTypeId && $currentId != $orderTypeId)
            $this->order_type()->updateOrCreate(
                ['car_id'           => $this->id],
                [
                    'order_type_id'     => $orderTypeId,
                    'author_id'         => auth()->user()->id
                ],
            );
    }



    /**
     * SAVE ORDER NUMBER
     */
    public function saveOrderNumber(string $orderNumber = null)
    {
        if ($orderNumber)
            $this->order()->updateOrCreate(
                ['car_id'       => $this->id],
                ['order_number' => $orderNumber]
            );
    }



    /**
     * SAVE MARKER
     */
    public function saveMarker(int $markerId = null)
    {
        if ($markerId)
            $this->marker()->updateOrCreate(['car_id' => $this->id], ['marker_id' => $markerId]);
    }



    /**
     * SAVE TRADE MARKER
     */
    public function saveTradeMarker(int $tradeMarkerId = null)
    {
        if ($tradeMarkerId)
            $this->trade_marker()->updateOrCreate(['car_id' => $this->id], ['trade_marker_id' => $tradeMarkerId]);
    }



    /**
     * SAVE PROVIDER
     */
    public function saveProvider(int $providerId = null)
    {
        if ($providerId)
            $this->provider()->updateOrCreate(['car_id' => $this->id], ['provider_id' => $providerId]);
    }



    /**
     * SAVE LOGISTIC DATES
     */
    // public function saveLogisticDates(LogisticDateDTO $dto)
    // {
    //     $states = LogisticState::get(); //Возможные шаги

    //     $arr = $dto->get(); //Превращаем объект дат в масив

    //     $dates = $this->logistic_dates; //существующие даты на машине

    //     $countDates = count($dates); //количество всех установленых дат

    //     $currentState = $countDates ? $dates->max('state.state') : 0; //текущий шаг на машине

    //     $dateTime = new DateTime(); //объект для форматирования даты

    //     $updateOrCreate = function ($key, $date) use ($dateTime) {
    //         $this->logistic_dates()->updateOrCreate(
    //             ['car_id' => $this->id, 'logistic_system_name' => $key,],
    //             ['author_id' => auth()->user()->id, 'date_at' => $dateTime->createFromFormat('d.m.Y', $date), 'logistic_system_name' => $key,]
    //         );
    //     };

    //     $maxState = 0;

    //     foreach ($arr as $key => $item) //бежим по массиву полученых дат полученых от клиента
    //     {
    //         if ($currentState == 0) //если на машине не было ни одной даты
    //             $updateOrCreate($key, $item); //сохраняем все что пришло с клиента

    //         else //иначе если хотя бы одна дата заполнена
    //         {
    //             $state = $states->where('system_name', $key)->first(); //узнаем цену шага

    //             $maxState = $state->state > $maxState ? $state->state : $maxState;

    //             $carLogisticState = $dates->where('logistic_system_name', $key)->first(); //получаем если есть дату из машины по текущему ключу

    //             if ($state->state > $currentState && $state->state > 0) //если цена шага больше либо равна цене текущего шага и больше 0
    //                 $updateOrCreate($key, $item); //то обновляем либо создаем

    //             elseif ($state->state == $currentState && $state->state != 0) {
    //                 //dump($carLogisticState->date_at->format('d.m.Y'));
    //                 //dump($item);
    //                 if ($carLogisticState == null || $carLogisticState && $carLogisticState->date_at->format('d.m.Y') != $item)
    //                     //dd(1);
    //                     $updateOrCreate($key, $item);
    //             } elseif ($state->state == 0) //если шаг логистики = 0, (те те которые всегда редактируемые)
    //             {
    //                 if ($carLogisticState == null || ($carLogisticState && $carLogisticState->date_at->format('d.m.Y') != $item)) //если логистики небыло или дата текущая != переданой с клиента
    //                     $updateOrCreate($key, $item); //то обновляем либо создаем
    //             }
    //         }
    //     }

    //     //$this->logistic_dates()->where('state.state', '>', $maxState)->delete();
    // }

    public function saveLogisticDates(LogisticDateDTO $dto)
    {
        $updateOrCreate = function ($key, $date) {
            $dateTime = new DateTime();

            $this->logistic_dates()->updateOrCreate(
                ['car_id' => $this->id, 'logistic_system_name' => $key,],
                [
                    'author_id' => auth()->user()->id,
                    'date_at' => $dateTime->createFromFormat('d.m.Y', $date),
                    'logistic_system_name' => $key,
                ]
            );
        };

        $dataFromClient = $dto->get();

        $carDates = ($this->logistic_dates);

        foreach ($carDates as $item)
            if (!array_key_exists($item->logistic_system_name, $dataFromClient))
                $item->delete();

        foreach ($dataFromClient as $key => $item)
            if (!$carDates->contains('logistic_system_name', $key))
                $updateOrCreate($key, $item);
            elseif ($carDates->where('logistic_system_name', $key)->first()->date_at->format('d.m.Y') != $item)
                $updateOrCreate($key, $item);
    }



    /**
     * SAVE TECHNIC
     */
    public function saveTechnic(int $technicId = null)
    {
        if ($technicId)
            $this->technic()->updateOrCreate(['car_id' => $this->id], ['technic_id' => $technicId]);
    }



    /**
     * SAVE AUDIO
     */
    public function saveAudio(string $audioCode = null)
    {
        if ($audioCode)
            $this->audio()->updateOrCreate(['car_id' => $this->id], ['audio_code' => $audioCode]);
    }



    /**
     * SAVE PURCHASE
     */
    public function savePurchase(int $cost = null)
    {
        $currentCost = $this->purchase->cost ?? null;

        if ($cost && $cost != $currentCost)
            $this->purchase()->updateOrCreate([
                'car_id' => $this->id
            ], [
                'cost' => $cost,
                'author_id' => auth()->user()->id
            ]);
    }



    /**
     * SAVE DELIVERY TERM
     */
    public function saveDeliveryTerm(int $delivery_term_id = null)
    {
        $currentDelivery = $this->delivery_terms->delivery_term_id ?? null;

        if (!$delivery_term_id || $delivery_term_id == $currentDelivery)
            return;

        $this->delivery_terms()->updateOrCreate(
            [
                'car_id' => $this->id,
            ],
            [
                'delivery_term_id'  => $delivery_term_id,
                'author_id'         => auth()->user()->id,
            ],
        );
    }



    /**
     * SAVE OPTIONS
     */
    public function saveOptions(array $optionArray = null)
    {
        if ($optionArray)
            $this->options()->sync($optionArray);
    }



    /**
     * SAVE DETAILING COST
     */
    public function saveDetailingCosts(array $detailingArray = null)
    {
        if ($detailingArray) {
            $this->detailing_costs()->delete();

            foreach ($detailingArray as $item) {
                if ($item['detailing_cost_id'])
                    $this->detailing_costs()->create(
                        [
                            'detailing_cost_id' => $item['detailing_cost_id'],
                            'price'             => $item['price'],
                            'coefficient'       => $item['coefficient'],
                        ],
                    );
            };
        }
    }



    /**
     * SAVE TUNING
     */
    public function saveTuning(CarTuningDTO $tuningArray = null)
    {
        $data = $tuningArray->get();

        if (!$data)
            return;

        $price = $data['price'];
        $devices = $data['devices'];

        if ($price >= 0 && $devices) {
            $this->tuning_price()->updateOrCreate(['car_id' => $this->id], ['price' => $price]);
            $this->tuning()->sync($devices);
        }
    }



    /**
     * SAVE OVER PRICE
     */
    public function saveOverPrice(int $overPrice = null)
    {
        $currentPrice = $this->over_price->price ?? null;

        if ($overPrice && $overPrice != $currentPrice)
            $this->over_price()->updateOrCreate(['car_id' => $this->id], [
                'price' => $overPrice,
                'author_id' => auth()->user()->id,
            ]);
    }



    /**
     * SAVE COMMENT
     */
    public function saveComment(string $comment = null)
    {
        $isComment = $this->comment;

        if (($comment && $isComment && $this->comment->comment != $comment) || ($comment && $isComment == null)) {
            $this->comment()->updateOrCreate(['car_id' => $this->id], [
                'author_id' => auth()->user()->id,
                'comment' => $comment,
            ]);
        }
    }



    /**********************************************
    /******************************************* */
    /***GET METHOD////////////////////////////// */
    /******************************************* */
    /******************************************* */



    /**
     * GET LOGISTIC DATES
     */
    public function getLogisticDates(string $key = null, string $format = 'd.m.Y')
    {
        if ($key) {
            $date = $this->logistic_dates->where('logistic_system_name', $key)->first();

            if ($date)
                return $date->date_at->format($format);

            return '';
        }

        return $this->logistic_dates->map(function ($item) use ($format) {
            return [$item->logistic_system_name => $item->date_at->format($format)];
        });
    }



    /**
     * GET LOGISTIC DATE AUTHORS WITH CREATE DATE
     */
    public function getLogisticAuthors(string $key = null, $format = 'd.m.Y (H:i)')
    {
        if ($key) {
            $date = $this->logistic_dates->where('logistic_system_name', $key)->first();

            if ($date)
                return [
                    'author' => $date->author->cut_name,
                    'updated_at' => $date->updated_at->format($format),
                    'date_at' => $date->date_at->format('d.m.Y'),
                ];

            return [];
        }
        return $this->logistic_dates->map(function ($item) use ($format) {
            return [$item->logistic_system_name => [
                'author'        => $item->author->cut_name,
                'updated_at'    => $item->updated_at->format($format),
                'description'   => $item->state->name,
            ]];
        });
    }



    /**
     * СУММА ДЕТАЛИЗАЦИИ ЦЕНЫ
     */
    public function getDetailingFullCostAttribute()
    {
        $price = $this->detailing_costs->sum('price');
        return $price;
    }



    /**
     * IS FIXED PRICE
     */
    public function isFixedPrice(): bool
    {
        if ($this->reserve && $this->reserve->contract && $this->reserve->contract->dkp_offer_at)
            return 1;
        return 0;
    }



    public function getReserveCoast()
    {
        return $this->reserve ? $this->reserve->getCarCoast() : 0;
    }



    public function getReserveSale()
    {
        return $this->reserve ? $this->reserve->getCarSale() : 0;
    }



    public function getReserveBalance()
    {
        if (!$this->reserve)
            return 0;
        return $this->reserve->getBalance();
    }



    public function getReserveFullCoast()
    {
        return $this->reserve ? $this->reserve->getCarFullCoast() : 0;
    }



    public function reserveClient()
    {
        if (!$this->reserve)
            return new Client();

        $client = $this->reserve->worksheet->client;

        return $client;
    }



    public function reserveLastPayment()
    {
        if (!$this->reserve)
            return 0;
        return $this->reserve->payments->last();
    }



    public function getDKPOfferDate()
    {
        if (!$this->reserve || !$this->reserve->contract)
            return '';

        return $this->reserve->contract->DkpOfferDate;
    }



    public function getDKPAuthor()
    {
        if (!$this->reserve || !$this->reserve->contract)
            return '';

        return $this->reserve->contract->dkp_decorator->cut_name;
    }



    public function getReserveTradeins()
    {
        if (!$this->reserve)
            return collect([]);

        return $this->reserve->tradeins;
    }



    public function getWorksheetTradeins()
    {
        if (!$this->reserve || !$this->reserve->worksheet)
            return collect([]);

        return $this->reserve->worksheet->redemptions;
    }



    public function getCurrentState()
    {
        if (!$this->reserve)
            return 'Свободный';

        if ($this->reserve->contract)
            return 'Клиентский';

        $currentDate = now();
        $reserveDate = $this->reserve->created_at;
        $diffDate = $reserveDate->diffInDays($currentDate);

        return 'Резерв ' . $diffDate;
    }



    public function getCurrentStateColor()
    {
        if (!$this->reserve)
            return 'green';

        if ($this->reserve->contract)
            return 'orange';

        return 'red';
    }



    public function hasPTS()
    {
        return $this->getLogisticDates('ransom_date') ? 1 : 0;
    }



    public function complectationPrice()
    {
        if ($this->reserve)
            return $this->reserve->complectationPrice();

        return $this->full_price->complectationprice;
    }



    public function optionPrice()
    {
        if ($this->reserve)
            return $this->reserve->optionPrice();

        return $this->full_price->optionprice;
    }



    public function overPrice()
    {
        if ($this->reserve)
            return $this->reserve->overPrice();

        return $this->full_price->overprice;
    }



    public function tuningPrice()
    {
        if ($this->reserve)
            return $this->reserve->tuningPrice();

        return $this->full_price->tuningprice;
    }



    public function sale()
    {
        if ($this->reserve)
            return $this->reserve->getCarSale();
        return 0;
    }



    public function fullPrice()
    {
        return
            $this->complectationPrice() +
            $this->optionPrice() +
            $this->overPrice() +
            $this->tuningPrice();
    }



    public function currentCarState()
    {
        $state = $this->logistic_dates->where('state.state', $this->logistic_dates->max('state.state'))->first();

        $state_status = $this->state_status;

        $obj = new stdClass();
        $obj->title = $state_status->description ?? 'В заявке';
        $obj->date = $state->date_at ?? $this->created_at;
        $obj->id = $state_status->id ?? 0;

        if (isset($state->state)) {
            $obj->title = $state->state->carstate->description;
            $obj->date = $state->date_at ?? $this->created_at;
            $obj->id = $state->state->carstate->id;
        }

        if ($this->reserve && $this->reserve->issue->date_at) {
            $obj->title = $state_status->description ?? 'Выдан';
            $obj->date = $state->date_at ?? $this->created_at;
            $obj->id = 9;
        }

        if ($this->reserve && $this->reserve->sale->date_at) {
            $obj->title = $state_status->description ?? 'Продан';
            $obj->date = $state->date_at ?? $this->created_at;
            $obj->id = 10;
        }

        $now = now();

        $cutTimeFromDate = function ($obj) {
            $date = $obj->date;
            $date->setHour(0)->setMinutes(0)->setSeconds(0);
            $obj->date = $date;
        };

        $getWithCountDay = function ($obj, $plusDay = 0) use ($now) {
            $days = $now->diffInDays($obj->date) + $plusDay;
            return $obj->title . ' ' . $days . ' ' . StringHelper::dayWord($days);
        };

        $getWithDate = function ($obj) {
            return $obj->title . ' ' . $obj->date->format('d.m.Y');
        };

        $getWithCountDayOrDate = function ($obj) use ($now, $getWithCountDay, $getWithDate) {
            if ($now > $obj->date)
                return $getWithCountDay($obj);
            return $getWithDate($obj);
        };

        $getOnlyStatus = function ($obj) {
            return $obj->title;
        };

        $cutTimeFromDate($obj);

        return match ($obj->id) {
            0 => $getWithCountDay($obj, 1),
            1 => $getWithCountDay($obj, 1),
            2 => $getWithCountDayOrDate($obj),
            3 => $getWithDate($obj),
            4 => $getOnlyStatus($obj),
            5 => $getOnlyStatus($obj),
            6 => $getWithDate($obj),
            7 => $getWithDate($obj),
            8 => $getWithCountDay($obj, 1),
            9 => $getWithDate($obj),
            10 => $getWithDate($obj),
        };
    }



    public function getBodyDatePrice()
    {
        if ($this->reserve && $this->reserve->contract)
            return $this->reserve->contract->dkp_offer_date;

        return $this->complectation->current_price->begin_at ? $this->complectation->current_price->begin_at->format('d.m.Y') : '';
    }
}
