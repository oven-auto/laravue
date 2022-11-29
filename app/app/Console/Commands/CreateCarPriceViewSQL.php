<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class CreateCarPriceViewSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:carprice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать таблицу-представление для цены автомобиля';

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
        $query = "CREATE VIEW car_prices AS
            SELECT
                cars.id as car_id,
                IFNULL(complectations.price,0) as complectation_price,
                IFNULL(cars.device_price,0) as device_price,
                IFNULL(sum(packs.price),0) as pack_price,
                (
                    IFNULL(complectations.price,0) +
                    IFNULL(cars.device_price,0) +
                    IFNULL(sum(packs.price),0) -
                    IFNULL(cars.device_cost,0) -
                    IFNULL(cars.purchase,0)
                ) as margin_price,
                (IFNULL(complectations.price,0) + IFNULL(cars.device_price,0) +  IFNULL(sum(packs.price),0)) as full_price

            FROM cars
            LEFT JOIN complectations on complectations.id = cars.complectation_id
            LEFT JOIN car_packs on car_packs.car_id = cars.id
            LEFT JOIN packs on packs.id = car_packs.pack_id
            GROUP BY cars.id
        ";

        DB::statement($query);
    }
}
