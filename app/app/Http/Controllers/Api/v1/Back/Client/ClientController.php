<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Client\ClientRepository;
use App\Http\Resources\Client\ClientListCollection;
use App\Http\Resources\Client\ClientEditResource;
use App\Models\Client;

class ClientController extends Controller
{
    public function __construct(ClientRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $clients = $this->repo->filter($request->input(), 50);
        return new ClientListCollection($clients);
    }

    public function edit(Client $client)
    {
        return new ClientEditResource($client);
    }

    public function store(Client $client, Request $request)
    {
        $this->repo->save($client, $request->all());
        return new ClientEditResource($client);
    }

    public function update(Client $client, Request $request)
    {
        $this->repo->save($client, $request->all());
        return new ClientEditResource($client);
    }
}
