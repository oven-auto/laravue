<?php

namespace App\Jobs;

use App\Classes\LadaDNM\DNMEvent;
use App\Events\DNMVisitEvent;
use App\Models\WsmReserveNewCar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateDNMEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $reserve; 

    public $action;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WsmReserveNewCar $reserve, string $action)
    {
        $this->reserve = $reserve;

        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::alert('Пробую отправить событие '.$this->action.' в DNM');

        $service = (new DNMEvent())->handler($this->reserve, $this->action);

        Log::alert('Завершил отправку события '.$this->action.' в DNM');
    }
}
