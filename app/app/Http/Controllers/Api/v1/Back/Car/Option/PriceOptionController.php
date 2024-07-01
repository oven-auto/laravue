<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Option;

use App\Helpers\String\StringHelper;
use App\Http\Controllers\Controller;
use App\Models\OptionPrice;
use App\Repositories\Car\Option\PriceOptionRepository;
use Illuminate\Http\Request;

class PriceOptionController extends Controller
{
    private $repo;

    public function __construct(PriceOptionRepository $repo)
    {
        $this->repo = $repo;
    }



    public function index(Request $request)
    {
        $validated = $request->validate([
            'option_id' => 'required|numeric'
        ]);

        $result = $this->repo->get($validated);

        return response()->json([
            'data' => $result->map(function ($item) {
                return  [
                    'id' => $item->id,
                    'option_id' => $item->complectation_id,
                    'price' => StringHelper::moneyMask($item->price),
                    'begin_at' => $item->begin_at->format('d.m.Y'),
                    'author' => $item->author->cut_name,
                ];
            })
        ]);
    }



    public function store(OptionPrice $optionPrice, Request $request)
    {
        $this->repo->save($optionPrice, $request->all());

        return response()->json([
            'data' => [
                'id'            => $optionPrice->id,
                'price'         => $optionPrice->price,
                'begin_at'      => $optionPrice->begin_at->format('d.m.Y'),
                'author'        => $optionPrice->author->cut_name,
                'created_at'    => $optionPrice->created_at->format('d.m.Y'),
            ],
            'success' => 1,
        ]);
    }



    public function update(OptionPrice $optionPrice, Request $request)
    {
        $this->repo->save($optionPrice, $request->all());

        return response()->json([
            'data' => [
                'id'            => $optionPrice->id,
                'price'         => $optionPrice->price,
                'begin_at'      => $optionPrice->begin_at->format('d.m.Y'),
                'author'        => $optionPrice->author->cut_name,
                'created_at'    => $optionPrice->created_at->format('d.m.Y'),
            ],
            'success' => 1,
        ]);
    }



    public function show(OptionPrice $optionPrice)
    {
        return response()->json([
            'data' => [
                'id'            => $optionPrice->id,
                'price'         => $optionPrice->price,
                'begin_at'      => $optionPrice->begin_at->format('d.m.Y'),
                'author'        => $optionPrice->author->cut_name,
                'created_at'    => $optionPrice->created_at->format('d.m.Y'),
            ],
            'success' => 1,
        ]);
    }
}
