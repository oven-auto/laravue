<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class CreateCarDevicesViewSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:carequipments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создаст таблицу-представление в которой будут содержаться все оборудование автомобиля';

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
        $query = "CREATE VIEW car_equipments AS
            SELECT DISTINCT cars.id as car_id, devices.id as device_id, devices.device_filter_id
            FROM cars
            LEFT JOIN complectation_devices on complectation_devices.complectation_id = cars.complectation_id
            LEFT JOIN car_packs on car_packs.car_id = cars.id
            LEFT JOIN pack_devices on pack_devices.pack_id = car_packs.pack_id
            JOIN devices on devices.id = complectation_devices.device_id or devices.id = pack_devices.device_id
            WHERE cars.deleted_at IS NULL
            ORDER BY cars.id
        ";

        DB::statement($query);
    }
}
