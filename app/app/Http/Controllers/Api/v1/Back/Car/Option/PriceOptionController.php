<?php

namespace App\Http\Controllers\Api\v1\Back\Car\Option;

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
            'option_id' => 'required|numeric',
            'car_id'    => 'sometimes|numeric'
        ]);

        if (!$validated)
            throw new \Exception('Не указан параметр.');

        $result = $this->repo->get($validated);

        return response()->json([
            'data' => $result,
            'success' => 1,
        ]);
    }



    public function store(OptionPrice $optionPrice, Request $request)
    {
        $validated = $request->validate([
            'option_id'     => 'required',
            'price'         => 'required',
            'begin_at'      => 'required'
        ]);

        $this->repo->save($optionPrice, $validated);

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
        return response()->json([
            'message' => 'Изменение опции не допустимо',
            'success' => 0,
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
