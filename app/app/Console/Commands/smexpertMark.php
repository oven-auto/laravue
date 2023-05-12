<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class smexpertMark extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smexpert:markfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Залить таблицу модели данными из SMExpert';

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
        $token = \App\Classes\SMExpert\Token::getInstance()->getToken();

        $url = 'https://appraisal.api.cm.expert/v1/autocatalog/models';

        $brands = \App\Models\Brand::select(['uid','id'])->get();

        $ladaModel = [
            1602,
            3641,
            3642,
            2004,
            4148,
            4180,
            3819,
            3957,
            4074,
            4071,
            4073,
            4072
        ];

        foreach($brands as $itemBrand)
        {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get($url, [
                'brand' => $itemBrand->uid
            ]);

            foreach($response['models'] as $itemModel){
                if(!in_array($itemModel['id'], $ladaModel))
                    \App\Models\Mark::create([
                        'uid' => $itemModel['id'],
                        'name' => $itemModel['text'],
                        'slug' => \Str::slug($itemModel['text']),
                        'brand_id' => $itemBrand->id,
                        'brand_uid' => $itemBrand->uid,
                        'status' => 0
                    ]);
            }
        }
    }
}
