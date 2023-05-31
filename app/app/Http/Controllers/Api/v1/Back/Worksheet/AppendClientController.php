<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class AppendClientController extends Controller
{
    public $repo;

    public function __construct(\App\Repositories\Client\ClientUnionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function append(Request $request)
    {
        $worksheet = Worksheet::findOrFail($request->get('worksheet_id'));
        $ids = $worksheet->subclients->pluck('id')->toArray();
        array_push($ids, $request->get('client_id'));
        $subClient = \App\Models\Client::find($request->get('client_id'));
        $client = $worksheet->client;
        $worksheet->subclients()->sync($ids);
        $this->repo->addUnion($client, $request->get('client_id'));
        return response()->json([
            'success' => 1,
            'client' => [
                'id' => $subClient->id,
                'name' => $subClient->full_name,
            ],
            'message' => 'Клиент добавлен'
        ]);
    }

    public function destroy(Request $request)
    {
        $worksheet = Worksheet::findOrFail($request->get('worksheet_id'));
        $worksheet->subclients()->detach($request->get('client_id'));
        $client = $worksheet->client;
        $this->repo->delUnion($client, $request->get('client_id'));
        return response()->json([
            'success' => 1,
            'message' => 'Клиент удален'
        ]);
    }
}
