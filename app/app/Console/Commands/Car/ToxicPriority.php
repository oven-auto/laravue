<?php

namespace App\Console\Commands\Car;

use App\Classes\Car\CarPriority\CarPriority;
use App\Models\Car;
use Illuminate\Console\Command;

class ToxicPriority extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'car:toxic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cars = Car::query()
            ->leftJoin('car_status_types', 'car_status_types.car_id', 'cars.id')
            ->whereIn('car_status_types.status', ['free', 'reserved', 'client'])
            ->where('cars.status', 'is_stock')
            ->get();
        dd($cars);
        // $cars->each(function($itemCar){
        //     CarPriority::make($itemCar)->checkPriority();
        // });
    }
}
