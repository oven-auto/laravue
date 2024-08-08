<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WsmReserveNewCarContract extends Model
{
    use HasFactory, Filterable;

    public $dates = [
        'pdkp_offer_at', 'pdkp_delivery_at', 'pdkp_closed_at', 'dkp_offer_at', 'dkp_closed_at'
    ];

    protected $casts = [
        'pdkp_offer_at'         => 'datetime:d.m.Y',
        'pdkp_delivery_at'      => 'datetime:d.m.Y',
        'pdkp_closed_at'        => 'datetime:d.m.Y',
        'dkp_offer_at'          => 'datetime:d.m.Y',
        'dkp_closed_at'         => 'datetime:d.m.Y',
        'updated_at'            => 'datetime:d.m.Y',
    ];

    protected $guarded = [];

    private const PDKP_STATUSES = [
        0 => '',
        1 => 'Действует',
        2 => 'Просрочен',
        3 => 'Исполнен',
        4 => 'Расторгнут'
    ];



    /**
     * ACCESSORS
     */

    public function getPdkpOfferDateAttribute()
    {
        return $this->pdkp_offer_at ? $this->pdkp_offer_at->format('d.m.Y') : '';
    }



    public function getPdkpDeliveryDateAttribute()
    {
        return $this->pdkp_delivery_at ? $this->pdkp_delivery_at->format('d.m.Y') : '';
    }



    public function getPdkpCloseDateAttribute()
    {
        return $this->pdkp_closed_at ? $this->pdkp_closed_at->format('d.m.Y') : '';
    }



    public function getDkpOfferDateAttribute()
    {
        return $this->dkp_offer_at ? $this->dkp_offer_at->format('d.m.Y') : '';
    }



    public function getDkpCloseDateAttribute()
    {
        return $this->dkp_closed_at ? $this->dkp_closed_at->format('d.m.Y') : '';
    }



    public function getUpdatedDateAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('d.m.Y') : '';
    }



    /**
     * RELATIONS
     */



    public function complectation_price()
    {
        return $this->belongsToMany(
            \App\Models\ComplectationPrice::class,
            'wsm_reserve_complectation_prices',
            'contract_id'
        );
    }



    public function option_price()
    {
        return $this->belongsToMany(
            \App\Models\OptionPrice::class,
            'wsm_reserve_option_prices',
            'contract_id'
        );
    }



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function reserve()
    {
        return $this->hasOne(\App\Models\WsmReserveNewCar::class, 'id', 'reserve_id');
    }



    public function pdkp_decorator()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'pdkp_decorator_id')->withDefault();
    }



    public function dkp_decorator()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'dkp_decorator_id')->withDefault();
    }



    /**
     * METHODS
     */



    /**
     * ПРОВЕРКА НА ОТКРЫТЫЕ ДОГОВОРЫ, ЕСЛИ ЕСТЬ ХОТЯ БЫ ОДНА ИЗ ДАТ ОФОРМЛЕНИЯ
     * И ОТСУТСТВУЮТ ОБЕ ДАТЫ ЗАКРЫТИЯ ТО СЧИТАТЬ ОТКРЫТЫМ
     */
    public function isWorking()
    {
        $pdkpOffer = $this->pdkp_offer_at;
        $pdkpClose = $this->pdkp_closed_at;

        $dkpOffer = $this->dkp_offer_at;
        $dkpClose = $this->dkp_closed_at;

        if ($pdkpOffer || $dkpOffer)
            if ($pdkpClose == null || $dkpClose == null)
                return 1;
        return 0;
    }



    /**
     * Кол-во дней просрока ПДКП
     */
    public function pdkdDays()
    {
        if ($this->pdkp_offer_at && $this->pdkp_delivery_at)
            return abs($this->pdkp_delivery_at->diff($this->pdkp_offer_at)->days);
        return 0;
    }



    /**
     * Получить статус ПДКП
     */
    public function getPdkpStatus()
    {
        if ($this->pdkpCloseDate)
            return 4;

        if ($this->dkpOfferDate)
            return 3;

        if ($this->pdkp_delivery_date_at < now())
            return 2;

        else
            return 1;

        return 0;
    }



    /**
     * Получить статус ДКП
     */
    public function getDKPStatus(): string
    {
        if ($this->dkpCloseDate)
            return 4;

        if ($this->reserve->isSaled())
            return 3;

        else
            return 1;

        return 0;
    }



    /**
     * Получить строку статуса ПДКП
     */
    public function getPDKPStatusString(): string
    {
        return self::PDKP_STATUSES[$this->getPdkpStatus()];
    }



    /**
     * Получить строку статуса ДКП
     */
    public function getDKPStatusString(): string
    {
        return self::PDKP_STATUSES[$this->getDkpStatus()];
    }



    /**
     * Получить дебиторскую задолженность
     */
    public function getDebtorArrears()
    {
        if ($this->dkpCloseDate || $this->pdkpCloseDate)
            return 0;
        return $this->reserve->getDebt();
    }



    /**
     * Получить кредиторскую задолженность
     */
    public function getCreditorArrears()
    {
        if ($this->reserve->isSaled())
            return 0;
        return $this->reserve->getPaymentSum();
    }
}
