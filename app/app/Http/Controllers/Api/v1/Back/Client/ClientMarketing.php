<?php

namespace App\Http\Controllers\Api\v1\Back\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientMarketing extends Controller
{
    private $repo;
    public function __construct(\App\Repositories\Client\ClientEventRepository $repo)
    {
        $this->repo = $repo;
    }

    public function __invoke(Request $request)
    {
        if(!$request->has('client_id'))
            throw new \Exception('Не указан клиент');
        $data = $this->repo->getAllInGroupByClientId($request->get('client_id'));

        return new \App\Http\Resources\Client\EventListCollection($data);
    }
}
