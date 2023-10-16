<?php

namespace App\Http\Controllers\Api\v1\Back\TaskList;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskList\EventListCollection;
use App\Repositories\Client\ClientEventRepository;
use Illuminate\Http\Request;

class EventListController extends Controller
{
    public function __invoke(ClientEventRepository $repo, Request $request)
    {
        $events = $repo->get($request->input());

        return (new EventListCollection($events))
            ->additional(['request' => $request->all()]);
    }
}
