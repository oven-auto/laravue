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

        return response()->json([
            'success' => 1
        ]);
    }
}
