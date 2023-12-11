<?php

namespace App\Http\Controllers\Api\v1\Back\TaskList;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskList\TraficListCollection;
use App\Repositories\Trafic\TraficRepository;
use Illuminate\Http\Request;

class TraficListController extends Controller
{
    public function __invoke(TraficRepository $repo, Request $request)
    {
        $trafics = $repo->getTraficsForTaskList($request->all());

        return (new TraficListCollection($trafics))
            ->additional([
                'request' => $request->all(),
            ]);
    }
}
