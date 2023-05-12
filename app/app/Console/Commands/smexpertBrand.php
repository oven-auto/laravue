<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class smexpertBrand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smexpert:brandfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $url = 'https://appraisal.api.cm.expert/v1/autocatalog/brands';

        $response = Http::withHeaders([
            'Authorization' => $token
        ])->get($url, []);

        foreach($response['brands'] as $item)
            \App\Models\Brand::create([
                'uid' => $item['id'],
                'name' => $item['text'],
                'slug' => \Str::slug($item['text'])
            ]);

    }
}
