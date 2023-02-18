<?php

namespace App\Repositories\Client;

use App\Models\Client;
use App\Http\Filters\ClientFilter;
use App\Models\Trafic;

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

    public function findOrCreate(Trafic $trafic)
    {
        $client = Client::with(['phones' => function($query) use ($trafic){
            $query->where('phone', $trafic->phone);
        }])->firstOrCreate([
            'lastname'      => $client->lastname ?? $trafic->lastname,
            'firstname'     => $client->firstname ?? $trafic->firstname,
            'fathername'    => $client->fathername ?? $trafic->fathername
        ]);

        if($client->wasRecentlyCreated) {
            $client->phones()->create([
                'phone' => $trafic->phone
            ]);
            $client->emails()->create([
                'email' => $trafic->email
            ]);
        }

        return $client;
    }
}
