<?php

namespace App\Http\Controllers\Api\v1\SMExpert\Gain;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MarkController extends Controller
{
    public function __invoke()
    {
        $token = \App\Classes\SMExpert\Token::getInstance()->getToken();

        $url = 'https://appraisal.api.cm.expert/v1/autocatalog/models';

        $brands = \App\Models\Brand::select(['uid','id'])->get();

        foreach($brands as $itemBrand)
        {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->get($url, [
                'brand' => $itemBrand->uid
            ]);

            foreach($response['models'] as $itemModel)
                \App\Models\Mark::create([
                    'uid' => $itemModel['id'],
                    'name' => $itemModel['text'],
                    'slug' => \Str::slug($itemModel['text']),
                    'brand_id' => $itemBrand->id,
                ]);
        }

        // foreach($response['brands'] as $item)
        //     \App\Models\Mark::create([
        //         'uid' => $item['id'],
        //         'name' => $item['text'],
        //         'slug' => \Str::slug($item['text'])
        //     ]);

        return response()->json([
            'success' => 12
        ]);
    }
}
