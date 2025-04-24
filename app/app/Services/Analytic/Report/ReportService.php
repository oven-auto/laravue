<?php

namespace App\Services\Analytic\Report;

use App\Http\Filters\ReserveNewCarFilter;
use App\Models\WsmReserveNewCar;

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
            'has_debit' => 1 //у которых долг > 0
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
            'type_statuses' => ['issued'], //только клиентские и резерв
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
            'type_statuses' => ['saled'], //только проданные
            //'sale_date' => ['02.02.2021', '31.12.2025'],
        ];

        $filter = app()->make(ReserveNewCarFilter::class, ['queryParams' => $data]);

        $query = $this->setQuery();

        $query->filter($filter);

        $sales = $query->get();
        
        return $sales;
    }



    public function getReceiptReport(array $data)
    {
        
    }
}