<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class insertBodyWorksFromCM extends Command
{
    private const ACRONYMS = [
        13 => 'sed',
        21 =>'hatch',
        15 => 'wag',
        4 => 'off',
        11 => 'pick',
        6 => 'coupe',
        5 => 'conv',
        10 => 'miniwan',
        9 => 'minibus',
        17 => 'limousine',
        8 => 'cargovan',
        19 => 'chassis',
        25 => 'fbtruck',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cm:bodies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get bodies from CMExpert';

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
        print(':::::::::КУЗОВА:::::::::'."\n\r");

        $token = \App\Classes\SMExpert\Token::getInstance()->getToken();

        $urlBodies = 'https://appraisal.api.cm.expert/v1/autocatalog/bodies';
        $responseBodies = Http::withHeaders([
            'Authorization' => $token
        ])->get($urlBodies, []);

        if(!$responseBodies->ok() && !isset($responseBodies['bodies']))
        {
            print("Не удалось получить данные по Api");
            return;
        }

        $currentBodies = \App\Models\BodyWork::get();

        $responseBodies = collect($responseBodies['bodies']);

        $responseBodies->collect()->each(function($item, $key) use ($currentBodies){

            $item = (object) $item;

            if(!$currentBodies->contains('id', $item->id))
            {
                \App\Models\BodyWork::insert([
                    'id' => $item->id,
                    'name' => $item->text,
                    'acronym' => self::ACRONYMS[$item->id],
                ]);
                print('Добавил тип кузова: '.$item->text)."\n\r";
            }
        });
    }
}



