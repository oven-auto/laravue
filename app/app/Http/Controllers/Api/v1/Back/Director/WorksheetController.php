<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Helpers\Date\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Filters\WorksheetFilter;
use App\Models\Worksheet;
use App\Repositories\Worksheet\WorksheetRepository;
use App\Services\Analytic\AnalyticWorksheet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorksheetController extends Controller
{
    public function __invoke(Request $request, WorksheetRepository $repo, AnalyticWorksheet $analytic)
    {
        if(!$request->has('appeal_ids'))
            $request->merge(['appeal_ids' => auth()->user()->appeals->map(fn($item) => [
                $item->id
            ])->toArray()]);

        $dataCreated = $request->all();
        $dataClosed = $request->all();

        if($request->has('show_month'))
        {
            $dataCreated    = array_merge($dataCreated, ['created_month' => $request->get('show_month')]);
            $dataClosed     = array_merge($dataClosed, ['closed_month' => $request->get('show_month')]);
        }

        $col = [];
        if($request->has('show_month'))
        {
            $date = new Carbon($request->show_month);
            $col = [
                    'Кол-во',
                    'Доля',
                    DateHelper::russianMonth($date->subMonth()->month-1).' '.date('y'),
                    'Динамика',
                    DateHelper::russianMonth($date->month).' '.(date('y')-1),
                    'Динамика',
            ];
        }

        return response()->json([
            'col' => $col,
            'data' => [

                'created'   => $analytic->fasade($dataCreated, new \App\Services\Analytic\CreatedWorksheetAnalytic()),
                'closed'    => $analytic->fasade($dataClosed, new \App\Services\Analytic\ClosedWorksheetAnalytic()),
                'results'   => $analytic->fasade($dataClosed, new \App\Services\Analytic\ResultWorksheetAnalytic()),
                'work'      => $repo->workingCount($request->except(['register_begin', 'register_end']))
            ],
            'success' => 1,
        ]);
    }
}
