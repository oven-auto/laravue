<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class insertEnginesFromCM extends Command
{
    private const ACRONYMS = [
        1 => 'petrol',
        2 => 'diesel',
        3 => 'hybrid',
        4 => 'electric',
        5 => 'gas',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cm:engines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get engines from CMExpert';

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
        print("\n\r");
        print(':::::::::МОТОРЫ:::::::::'."\n\r");

        $token = \App\Classes\SMExpert\Token::getInstance()->getToken();

        $url = 'https://appraisal.api.cm.expert/v1/autocatalog/engines';
        $response = Http::withHeaders([
            'Authorization' => $token
        ])->get($url, []);

        if(!$response->ok() && !isset($response['engines']))
        {
            print("Не удалось получить данные по Api");
            return;
        }

        $current = \App\Models\MotorType::get();

        $responseDrivers = collect($response['engines']);

        $responseDrivers->collect()->each(function($item, $key) use ($current){

            $item = (object) $item;

            if(!$current->contains('id', $item->id))
            {
                \App\Models\MotorType::insert([
                    'id' => $item->id,
                    'name' => $item->text,
                    'acronym' => self::ACRONYMS[$item->id],
                ]);
                print('Добавил тип мотора: '.$item->text)."\n\r";
            }
        });
    }
}
