<?php

namespace App\Console\Commands;

use App\Models\CarStatusType;
use Illuminate\Console\Command;

class SQL_VIEW_CAR_STATUS_TYPE extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:carstatustype';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать представление, для получения текущего типа статуса (свободный, резер, клиентский, продан)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $query = "CREATE OR REPLACE VIEW car_status_types AS
            SELECT cars.id as car_id,

            IF(
                reserves.id is NULL, '" . CarStatusType::VALUES['free'] . "', IF(
                    contracts.id is NULL, '" . CarStatusType::VALUES['reserved'] . "', if(
                        sale.id is NULL, '" . CarStatusType::VALUES['client'] . "', '" . CarStatusType::VALUES['saled'] . "'
                    )
                )
            ) as status

            FROM `cars`
            LEFT JOIN wsm_reserve_new_cars as reserves
                on reserves.car_id = cars.id
            LEFT JOIN wsm_reserve_new_car_contracts as contracts
                on contracts.reserve_id = reserves.id
            LEFT JOIN wsm_reserve_sales as sale
                on sale.reserve_id = reserves.id

            WHERE (reserves.deleted_at IS NULL)";

        echo ('Создаю/Изменяю представление: car_status_types' . "\r\n");
        echo ('Данное представление хранит текущий тип статуса автомобиля(нового) и содержит следующие столбцы:' . "\r\n");
        echo ('1) car_id - идентификатор из таблицы cars,' . "\r\n");
        echo ('2) status - собственно текущий тип статуса автомобиля(нового), ' . "\r\n");

        \DB::statement($query);

        echo ('Закончил работу с car_status_types' . "\r\n" . "\r\n");
    }
}
