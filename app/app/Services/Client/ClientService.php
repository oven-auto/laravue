<?php

namespace App\Services\Client;

use App\Models\Client;
use App\Models\Trafic;

Class ClientService
{
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
