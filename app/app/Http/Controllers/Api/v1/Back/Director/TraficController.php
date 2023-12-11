<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Helpers\Date\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;
use App\Models\User;
use App\Repositories\Trafic\TraficRepository;
use App\Services\Analytic\AnalyticTrafic;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TraficController extends Controller
{
    public function __invoke(Request $request, TraficRepository $repo, AnalyticTrafic $analytic)
    {
            $filterOption['appeal_ids'] = auth()->user()->appeals->map(fn($item) => [
                'name' => $item->name,
                'id' => $item->id
            ]);

            $filterOption['structure_ids'] = auth()->user()->structures->map(fn($item) => [
                'name' => $item->structure->structure->name.' ('.$item->company->name.')',
                'id' => $item->company_structure_id,
                'company_id' => $item->company_id
            ]);

            $filterOption['company_ids'] = auth()->user()->companies->unique()->map(fn($item) => [
                'name' => $item->name,
                'id' => $item->id
            ]);


        return response()->json([

            'option' => $filterOption,

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
