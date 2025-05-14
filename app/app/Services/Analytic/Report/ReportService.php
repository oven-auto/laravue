<?php

namespace App\Services\Analytic\Report;

use App\Helpers\Date\DateHelper;
use App\Http\Filters\ReserveNewCarFilter;
use App\Models\WsmReserveNewCar;
use App\Models\WsmReservePayment;

Class ReportService
{
    public function setQuery()
    {
        $query = WsmReserveNewCar::select( 
                'wsm_reserve_new_cars.*',
            )            
            ->with([
                'worksheet' => function($q){
                    $q->with([
                        'client',
                        'author'
                    ]);
                }, 
                'car' => function($q){
                    $q->with([
                        'mark',
                        'complectation.current_price',
                        'options.current_price',
                        'collector',
                        'priority.sale_priority',
                        'logistic_dates',
                    ]);
                },
                'payments',
                'sale',
                'last_comment',
            ]);

        return $query;
    }



    /**
     * Клиенты в работе
     */
    public function getWorkedReport(array $data)
    {   
        $data = [
            'logistic_statuses' => ['in_stock'], //на складе
            'type_statuses' => ['reserved', 'client'], //только клиентские и резерв
            'has_debit' => 1, //у которых долг > 0
            'has_paid_date' => 0
        ];

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => $data]);

        $query = $this->setQuery();

        $query->filter($filter);

        $sales = $query->get();
        
        return $sales;
    }



    /**
     * План поступлений
     */
    public function getPlannedReport(array $data)
    {
        $data = [
            'type_statuses' => ['reserved', 'client'], //только клиентские и резерв
            'has_debit' => 1 //у которых долг > 0
        ];

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => $data]);

        $query = $this->setQuery();

        $query->filter($filter);

        $sales = $query->get();
        
        return $sales;
    }



    /**
     * Выдача с долгом
     */
    public function getWithDebitReport(array $data)
    {
        $data = [
            'type_statuses' => ['issued', 'saled'], //только клиентские и резерв
            'has_debit' => 1 //у которых долг > 0
        ];

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => $data]);

        $query = $this->setQuery();

        $query->filter($filter);

        $sales = $query->get();
        
        return $sales;
    }



    /**
     * Полные оплаты
     */
    public function getPaidReport(array $data)
    {
        $data = [
            'type_statuses' => ['client'], //только клиентские и резерв
            'has_debit' => 0 //у которых долг > 0
        ];

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => $data]);

        $query = $this->setQuery();

        $query->filter($filter);

        $sales = $query->get();
        
        return $sales;
    }



    /**
     * Выдачи
     */
    public function getIssuedReport(array $data)
    {
        $data = [
            'type_statuses' => ['issued'], //только выданные
        ];
        
        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => $data]);

        $query = $this->setQuery();

        $query->filter($filter);

        $sales = $query->get();
        
        return $sales;
    }



    /**
     * Продажи
     */
    public function getSaledReport(array $data)
    {
        $data = [
            'type_statuses' => ['saled'], 
            'sale_date' => $data['intervals'][0],
        ];
        
        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => $data]);

        $query = $this->setQuery();

        $query->filter($filter);

        $sales = $query->get();
        
        return $sales;
    }



    public function getReceiptReport(array $data)
    {
        $intervals = $data['intervals'][0];
        
        $query = $this->setQuery();

        $query->leftJoin('wsm_reserve_payments as _rpay', '_rpay.reserve_id', 'wsm_reserve_new_cars.id');             

        $reseipts = $query->whereBetween('_rpay.date_at', [
            DateHelper::createFromString($intervals[0])->setHour(0)->setMinute(0), 
            DateHelper::createFromString($intervals[1] ?? $intervals[0])->setHour(23)->setMinute(59)
        ])
            ->groupBy('_rpay.id')
            ->get();

        $res = $reseipts->each(function($itemReserve){
            $pays = $itemReserve->payments;

            $res = $pays->each(function($itemPay) use($itemReserve){
                $obj = $itemReserve;
                $obj->payments = $itemPay;
                return $obj;
            });

            return $res;
        });
        
        return $res;
    }
}