<?php

namespace App\Jobs;

use App\Classes\LadaDNM\DNMAppealService;
use App\Models\WsmReserveNewCar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateDNMReserveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $reserve;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WsmReserveNewCar $reserve)
    {
        $this->reserve = $reserve;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::alert('Пробую отправить данные о РЕЗЕРВЕ на ДНМ');

        echo 'Отправляю в ДНМ данные о резерве'.PHP_EOL;

            print_r([
                'id_reserve' => $this->reserve->id,
                'firstname' => $this->reserve->worksheet->client->firstname,
                'lastname' => $this->reserve->worksheet->client->lastname,
            ]);

        $service = (new DNMAppealService())->save($this->reserve);
        
        Log::alert('Закончил отправлять данные о РЕЗЕРВЕ на ДНМ');
    }
}
