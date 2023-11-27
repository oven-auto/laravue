<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Helpers\Date\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;
use App\Repositories\Trafic\TraficRepository;
use App\Services\Analytic\AnalyticTrafic;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TraficController extends Controller
{
    public function __invoke(Request $request, TraficRepository $repo, AnalyticTrafic $analytic)
    {
        // if(!$request->has('appeal_ids'))
        //     $request->merge(['appeal_ids' => auth()->user()->appeals->map(fn($item) => [
        //         $item->id
        //     ])->toArray()]);

        // dump($request->get('appeal_ids'));
        // dump(auth()->user()->appeals->pluck('id'));

        // if(!$request->has('appeal_ids'))
        //     $request->merge( ['appeal_ids' => auth()->user()->appeals->pluck('id')->toArray()] );

        // if(!$request->has('structure_ids'))
        //     $request->merge(['structure_ids' => auth()->user()->structures->pluck('company_structure_id')]);

        // if(!$request->has('company_ids'))
        //     $request->merge(['company_ids' => auth()->user()->companies->pluck('id')->unique()->toArray()]);

        // if(!$request->has('manager_id'))
        //     $request->merge(['manager_id' => auth()->user()->colleagues()->pluck('id')->toArray()]);

        //$date = Carbon::createFromFormat('Y-m-d', $request->interval_begin)->format('d.m.y');



        return response()->json([

            'data' => [
                'total'     => $analytic->fasade($request->all(), new \App\Services\Analytic\TotalTraficAnalytic()),

                'deleted'   => $analytic->fasade($request->all(), new \App\Services\Analytic\DeleteTraficAnalytic()),

                'general'   => $analytic->fasade($request->all(), new \App\Services\Analytic\GeneralTraficAnalytic()),

                'target_total' => $analytic->fasade($request->all(), new \App\Services\Analytic\TargetTotalTraficAnalytic()),

                'target'    => $analytic->fasade($request->all(), new \App\Services\Analytic\TargetTraficAnalytic()),

                'author'    => $analytic->fasade($request->all(), new \App\Services\Analytic\AuthorTraficAnalytic()),

                'personal'  => $analytic->fasade($request->all(), new \App\Services\Analytic\PersonalTraficAnalytic()),
            ],
            'success' => 1,
        ]);
    }
}
