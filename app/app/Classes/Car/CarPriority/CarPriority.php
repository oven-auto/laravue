<?php

namespace App\Classes\Car\CarPriority;

use App\Models\Car;

Class CarPriority
{
    private const STATUSES = [
        'preorder' => 1,                                //предзаказ
        'newentry' => 2,                                //свежее поступление
        'paidentry' => 3,                               //платный период
        'overdueentry' => 4,                            //просроченная дебиторка
        'problem' => 5,                                 //проблемный склад
        'toxic' => 6,                                   //токсичный склад
    ];

    protected $car;



    public function __construct(Car $car)
    {
        $this->car = $car;
    }



    private function setPriority(string $key) : void
    {
        $this->car->priority->fill([
            'priority_id' => self::STATUSES[$key]
        ])->save();
    }



    private function hasPaidDate()
    {
        return $this->car->paid_date ? 1 : 0;
    }



    private function hasControllPaidDate()
    {
        return $this->car->control_paid_date ? 1 : 0;
    }



    private function hasStockDate()
    {
        return $this->car->stockDate() ? 1 : 0;
    }



    public function checkPriority()
    {
        if(!($this->car instanceof \App\Models\Car))
            return;

        $now = now();
        $car = &$this->car;

        if($car->isReserved() && $this->hasStockDate() && $car->reserve->created_at < $car->stockDate())
        {
            $this->setPriority('preorder');
            return;
        }

        if($car->isReserved())
            return;

        if(!$car->hasRansom())
        {
            if($this->hasPaidDate() && $this->hasControllPaidDate())
            {
                if($car->paid_date->date_at > $now && $car->control_paid_date->date_at > $now)
                    $this->setPriority('newentry');
                if($car->paid_date->date_at <= $now && $car->control_paid_date->date_at >= $now)
                    $this->setPriority('paidentry');
                if($car->paid_date->date_at <= $now && $car->control_paid_date->date_at <= $now)
                    $this->setPriority('overdueentry');
            }
        }
        if($car->hasRansom())
        {
            if($now->subDays(90) <= $car->stockDate())
                $this->setPriority('problem');
            if($now->subDays(90) > $car->stockDate())
                $this->setPriority('toxic');
        }
    }
}