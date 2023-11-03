<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Helpers\Date\DateHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Trafic\TraficRepository;
use App\Services\Analytic\AnalyticTrafic;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TraficController extends Controller
{
    public function __invoke(Request $request, TraficRepository $repo, AnalyticTrafic $analytic)
    {
        if(!$request->has('appeal_ids'))
            $request->merge(['appeal_ids' => auth()->user()->appeals->map(fn($item) => [
                $item->id
            ])->toArray()]);

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

                'author'    => $analytic->fasade($request->all(), new \App\Services\Analytic\AuthorTraficAnalytic()),

                'total'     => $repo->counter($request->all()),
                'general'   => $analytic->fasade($request->all(), new \App\Services\Analytic\GeneralTraficAnalytic()),
                'target'    => $analytic->fasade($request->all(), new \App\Services\Analytic\TargetTraficAnalytic()),
                'personal'  => $analytic->fasade($request->all(), new \App\Services\Analytic\PersonalTraficAnalytic()),

            ],
            'success' => 1,
        ]);
    }
}
