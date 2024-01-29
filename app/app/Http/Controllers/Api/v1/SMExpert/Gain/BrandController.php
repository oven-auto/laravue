<?php

namespace App\Http\Controllers\Api\v1\SMExpert\Gain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BrandController extends Controller
{
    public function __invoke()
    {
        $token = \App\Classes\SMExpert\Token::getInstance()->getToken();

        //$url = 'https://lk.cm.expert/api/v1/cars/appraisals/requests';
        //$url = 'https://lk.cm.expert/api/v1//cars/appraisals';
        $url = 'https://lk.cm.expert/api/v1/doc#!/105410941077108510821072/get_cars_appraisals';

        $response = Http::withHeaders([
            'Authorization' => $token
        ])->get($url, []);

        dd($response);

        // foreach($response['brands'] as $item)
        //     \App\Models\Brand::create([
        //         'uid' => $item['id'],
        //         'name' => $item['text'],
        //         'slug' => \Str::slug($item['text'])
        //     ]);

        // return response()->json([
        //     'success' => 1
        // ]);
    }
}
