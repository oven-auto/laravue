<?php

namespace App\Services\Client\Rating;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

Class RatingService
{
    /**
     * Получить самых активных клиентов
     */
    public function getMostActive(int $limit = 10)
    {
        $clients = Client::query()
            ->select([
                'clients.*'
            ])
            ->groupBy('clients.id')
            ->get();

        return $clients;
    }
}