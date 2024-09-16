<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WsmReserveNewCar extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $guarded = [];

    /**
     * RELATIONS
     */



    public function discounts()
    {
        return $this->morphMany(Discount::class, 'modulable')->where('modulable_type', $this::class);
    }



    public function tradeins()
    {
        return $this->belongsToMany(
            \App\Models\UsedCar::class,
            'wsm_reserve_trade_ins',
            'reserve_id',
            'used_car_id',
            'id'
        );
    }



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function car()
    {
        return $this->hasOne(\App\Models\Car::class, 'id', 'car_id');
    }



    public function worksheet()
    {
        return $this->hasOne(\App\Models\Worksheet::class, 'id', 'worksheet_id');
    }



    public function contract()
    {
        return $this->hasOne(\App\Models\WsmReserveNewCarContract::class, 'reserve_id', 'id')->withDefault();
    }



    public function sales()
    {
        return $this->hasMany(\App\Models\WsmReserveCarSale::class, 'reserve_id', 'id');
    }



    public function last_comment()
    {
        return $this->hasOne(\App\Models\WsmReserveComment::class, 'reserve_id', 'id')->orderBy('id', 'DESC')->withDefault();
    }



    public function payments()
    {
        return $this->hasMany(\App\Models\WsmReservePayment::class, 'reserve_id', 'id');
    }



    public function issue()
    {
        return $this->hasOne(\App\Models\WsmReserveIssue::class, 'reserve_id', 'id');
    }



    public function sale()
    {
        return $this->hasOne(\App\Models\WsmReserveSale::class, 'reserve_id', 'id');
    }



    /**NEW AFTER MODELS */



    /**
     * Проверка зафиксирована ли цена контракта
     */
    public function isFixedCost(): bool
    {
        if ($this->contract && $this->contract->dkp_offer_at)
            return 1;
        return 0;
    }



    /**
     * Получить дату на которую актуальна стоимость кузова
     */
    public function getCostDate(): string
    {
        if ($this->isFixedCost())
            return $this->contract->DkpOfferDate;
        return $this->car->complectation->priceDate;
    }



    /**
     * Получить стоимость кузова
     */
    public function getComplectationCost(): int
    {
        if ($this->isFixedCost())
            return $this->contract->complectation_price->sum('price');
        return $this->car->complectation->price;
    }



    /**
     * Получить стоимость опций
     */
    public function getOptionCost(): int
    {
        if ($this->isFixedCost())
            return $this->contract->option_price->sum('price') ?? 0;
        return $this->car->options->sum('price') ?? 0;
    }



    /**
     * Получить стоимость переоценки
     */
    public function getOverCost(): int
    {
        return $this->car->over_price->price ?? 0;
    }



    /**
     * Получить стоимость тюнинга
     */
    public function getTuningCost(): int
    {
        return $this->car->tuning_price->price ?? 0;
    }



    /**
     * Получить стоимость контракта
     */
    public function getFullCost(): int
    {
        return $this->getComplectationCost() +
            $this->getOptionCost() +
            $this->getOverCost() +
            $this->getTuningCost();
    }



    /**
     * Получить статус резерва
     */
    public function getStatus()
    {
        if ($this->isFixedCost())
            return  ['title' => 'Клиентский', 'color' => 1];

        $currentDate = now();
        $reserveDate = $this->created_at;
        $diffDate = $reserveDate->diffInDays($currentDate) + 1;
        return ['title' => 'Резерв ' . $diffDate, 'color' => 2];
    }



    /**
     * Получить скидки контракта
     * */
    public function getSaleSum(): int
    {
        return $this->discounts->sum('sum.amount') ?? 0;
    }



    /**
     * Получить сумму всех платежей
     */
    public function getPaymentSum(): int
    {
        $payments = $this->payments->sum('amount') ?? 0;
        $tradeins = $this->tradeins->sum('purchase_price') ?? 0;
        return $payments + $tradeins;
    }



    /**
     * Итоговая цена
     */
    public function getTotalCost(): int
    {
        return $this->getFullCost() - $this->getSaleSum();
    }



    /**
     * Задолженость
     */
    public function getDebt(): int
    {
        return $this->getTotalCost() - $this->getPaymentSum();
    }



    /**
     * Проверить была ли выдача
     */
    public function isIssued(): bool
    {
        return $this->issue ? 1 : 0;
    }



    /**
     * Проверить была ли продажа
     */
    public function isSaled(): bool
    {
        return $this->sale ? 1 : 0;
    }



    /**
     * Получить дату выдачи
     */
    public function getIssueDate(): string
    {
        return $this->isIssued() ? $this->issue->date_at->format('d.m.Y') : '';
    }



    /**
     * Получить дату выдачи
     */
    public function getSaleDate(): string
    {
        return $this->isSaled() ? $this->sale->date_at->format('d.m.Y') : '';
    }



    /**
     * Получить дату оформления ДКП
     */
    public function getDKPDate()
    {
        if ($this->isFixedCost())
            return $this->contract->DkpOfferDate;
        return '';
    }



    public function getReserveReportString()
    {
        return match ($this->getReserveReportStatus()) {
            0 => '',
            1 => 'Зеленый рапорт',
            2 => 'Желтый рапорт',
        };
    }



    public function getReserveReportStatus()
    {
        if (!$this->car->owner)
            return 0;

        $clientWorksheet = $this->worksheet->client_id;
        $clientCar = $this->car->owner->client_id;

        if ($clientCar == $clientWorksheet)
            return 1;

        else
            return 2;
    }
}
