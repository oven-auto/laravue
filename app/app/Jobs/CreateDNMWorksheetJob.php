<?php

namespace App\Jobs;

use App\Classes\LadaDNM\DNMClientService;
use App\Classes\LadaDNM\DNMWorksheetService;
use App\Events\ClientCreateOrUpdateEvent;
use App\Models\Worksheet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateDNMWorksheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $worksheet;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Worksheet $worksheet)
    {
        $this->worksheet = $worksheet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $worksheet = $this->worksheet;

        if($worksheet->isLada() && $worksheet->isSaleDepartment() && $worksheet->isSaleNewCar())
        {
            $this->worksheet->load('client');
            
            $service = (new DNMClientService())->save($this->worksheet->client);
         
            $service = (new DNMWorksheetService())->save($worksheet);
        }
    }
}
