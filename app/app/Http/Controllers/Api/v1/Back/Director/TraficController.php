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
