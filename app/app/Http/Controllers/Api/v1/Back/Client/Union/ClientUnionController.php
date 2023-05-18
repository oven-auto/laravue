<?php

namespace App\Http\Controllers\Api\v1\Back\Client\Union;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\UnionCollection;
use App\Models\Client;
use App\Repositories\Client\ClientRepository;
use Illuminate\Http\Request;

class ClientUnionController extends Controller
{
    public function show(Client $client, Request $request)
    {
        $data = $client->unionsChildren->merge($client->unionsParent);
        return new UnionCollection($data);
    }

    public function store(Client $client, Request $request)
    {
        if($request->client_id == $client->id)
            throw new \Exception('Клиент к которму Вы хотите добавить связь не может быть тем же кого добавляете');

        $unions = $client->unionsChildren->map(function($item){
            return $item->id;
        })->toArray();

        array_push($unions, $request->client_id);

        $unions = array_unique($unions);

        $client->unionsChildren()->sync($unions);

        $client = $client->fresh();

        return (new UnionCollection($client->unionsChildren))
            ->additional(['message' => 'Связь добавлена']);
    }

    public function destroy(Client $client, Request $request)
    {
        $client->unionsChildren()->detach($request->client_id);

        $client->unionsParent()->detach($request->client_id);

        $data = $client->unionsChildren->merge($client->unionsParent);

        return (new UnionCollection($data))
            ->additional(['message' => 'Связь удалена']);
    }

    public function amount($client)
    {
        $count = \App\Models\ClientUnion::where('client_id', $client)->orWhere('parent', $client)->count();

        return response()->json([
            'data' => $count,
            'success' => 1,
        ]);
    }

    public function search(Request $request, ClientRepository $repo)
    {
        $clients = $repo->paginate($request->input(), 50);
        return response()->json([
            'data' => $clients->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->full_name
                ];
            }),
            'success' => 1
        ]);
    }
}
