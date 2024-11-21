<?php

namespace App\Jobs;

use App\Classes\LadaDNM\DNMClientService;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateDNMClientJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $service;

    public $client;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::alert('Пробую отправить данные о клиенте на ДНМ');

        echo 'Отправляю в ДНМ данные клиента'.PHP_EOL;

        print_r([
            'id' => $this->client->id,
            'firstname' => $this->client->firstname,
            'lastname' => $this->client->lastname,
        ]);

        $service = (new DNMClientService())->save($this->client);

        Log::alert('Отправил данные о клиенте на ДНМ');
    }
}
