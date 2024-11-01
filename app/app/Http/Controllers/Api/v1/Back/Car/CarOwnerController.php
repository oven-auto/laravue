<?php

namespace App\Http\Controllers\Api\v1\Back\Car;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarOwnerController extends Controller
{
    public function store(Car $car, Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required'
        ]);

        $car->saveOwner($validated['client_id']);

        return response()->json([
            'data' => [
                'owner' => [
                    'name' => $car->owner->client->full_name,
                    'phone' => $car->owner->client->phones->first()->phone,
                ],
                'author' => $car->owner->author->cut_name,
                'created_at' => $car->owner->created_at->format('d.m.Y (H:i)'),
            ],
            'success' => 1
        ]);
    }



    public function destroy(Car $car, Request $request)
    {
        $car->owner()->delete();

        return response()->json([
            'success' => 1,
        ]);
    }
}
