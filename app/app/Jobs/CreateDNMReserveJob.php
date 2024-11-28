<?php

namespace App\Jobs;

use App\Classes\LadaDNM\DNMAppealService;
use App\Classes\LadaDNM\DNMClientService;
use App\Classes\LadaDNM\DNMEvent;
use App\Classes\LadaDNM\DNMWorksheetService;
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
        if($this->reserve->worksheet->isLada() && $this->reserve->worksheet->isSaleDepartment() && $this->reserve->worksheet->isSaleNewCar())
        {
            (new DNMClientService())->save($this->reserve->worksheet->client);

            (new DNMWorksheetService())->save($this->reserve->worksheet);

            (new DNMAppealService())->save($this->reserve);
            
            $action = match($this->reserve->worksheet->trafic->chanel->id) {
                1         => 'visit',
                2         => 'call',
                default     => 'internet',
            };

            (new DNMEvent())->handler($this->reserve, $action);
        }
    }
}
