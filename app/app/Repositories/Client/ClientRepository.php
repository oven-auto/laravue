<?php

namespace App\Repositories\Client;

use App\Models\Client;
use App\Http\Filters\ClientFilter;

Class ClientRepository
{
    public function filter($data = [], $paginate = 50)
    {
        $query = Client::select('clients.*');
        $filter = app()->make(ClientFilter::class, ['queryParams' => array_filter($data)]);
        $clients = $query->filter($filter)->paginate($paginate);
        return $clients;
    }

    public function save(Client $client, $data = [])
    {
        $client->fill($data)->save();
        return $client;
    }
}
