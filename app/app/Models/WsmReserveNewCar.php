<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WsmReserveNewCar extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * RELATIONS
     */
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
        return $this->hasOne(\App\Models\WsmReserveIssue::class, 'reserve_id', 'id')->withDefault();
    }



    public function sale()
    {
        return $this->hasOne(\App\Models\WsmReserveSale::class, 'reserve_id', 'id')->withDefault();
    }







    /**
     * METHODS
     */
    public function coast(): int
    {
        $carPrice = $this->car->full_price;

        $arrayPrice['overprice']            = $carPrice->overprice ?? 0;
        $arrayPrice['optionprice']          = $carPrice->optionprice ?? 0;
        $arrayPrice['complectationprice']   = $carPrice->complectationprice ?? 0;
        $arrayPrice['tuningprice']          = $carPrice->tuningprice ?? 0;

        if ($this->contract)
            $arrayPrice['complectationprice'] = $this->contract->price->complectationprice ?? 0;

        $sum = array_sum($arrayPrice);

        $sum -= $this->sales->sum('amount');

        $sum -= $this->tradeins->sum('purchase_price');

        return $sum;
    }



    //COMPLECTATION PRICE
    public function complectationPrice()
    {
        if ($this->contract)
            return $this->contract->price->complectationprice ?? 0;

        $carPrice = $this->car->full_price;

        return $carPrice->complectationprice ?? 0;
    }



    //OPTION PRICE
    public function optionPrice()
    {
        $carPrice = $this->car->full_price;

        return $carPrice->optionprice ?? 0;
    }



    //COMPLECTATION PRICE
    public function overPrice()
    {
        $carPrice = $this->car->full_price;

        return $carPrice->overprice ?? 0;
    }



    //COMPLECTATION PRICE
    public function tuningPrice()
    {
        $carPrice = $this->car->full_price;

        return $carPrice->tuningprice ?? 0;
    }



    /**
     * CAR COAST WITHOUT SALE
     */
    public function getCarCoast()
    {
        $carPrice = $this->car->full_price;

        $arrayPrice['overprice']            = $carPrice->overprice ?? 0;
        $arrayPrice['optionprice']          = $carPrice->optionprice ?? 0;
        $arrayPrice['complectationprice']   = $carPrice->complectationprice ?? 0;
        $arrayPrice['tuningprice']          = $carPrice->tuningprice ?? 0;

        if ($this->contract)
            $arrayPrice['complectationprice'] = $this->contract->price->complectationprice ?? 0;

        $sum = array_sum($arrayPrice);

        return $sum ?? 0;
    }



    public function getCarSale()
    {
        $sum = $this->sales->sum('amount');

        return $sum ?? 0;
    }



    public function getCarFullCoast()
    {
        return $this->getCarCoast() - $this->getCarSale();
    }



    public function getBalance()
    {
        $payments = $this->payments->sum('amount');
        $tradeins = $this->tradeins->sum('purchase_price');
        return $this->getCarFullCoast() - $payments - $tradeins;
    }



    public function getTotalPayments()
    {
        $payments = $this->payments->sum('amount');
        $tradeins = $this->tradeins->sum('purchase_price');
        return $payments + $tradeins;
    }
}
