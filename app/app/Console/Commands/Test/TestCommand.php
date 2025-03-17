<?php

namespace App\Console\Commands\Test;

use App\Classes\Car\CarPriority\CarPriority;
use App\Classes\Car\CarPriority\PrioritySetter;
use App\Classes\Car\CarPriority\Test;
use App\Http\Filters\CarFilter;
use App\Models\Car;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-priority';

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
        $data = [
            'has_stock_date' => 1,
        ];

        $query = Car::query()->select('cars.*');
        
        $filter = app()->make(CarFilter::class, ['queryParams' => $data]);
        
        $query->filter($filter);

        $car = $query->first();

        $this->info($car->id);

        $service = CarPriority::make($car);

        $service->checkPriority();
    }
}