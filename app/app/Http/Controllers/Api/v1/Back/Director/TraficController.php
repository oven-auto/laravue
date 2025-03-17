<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Helpers\Date\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Filters\TraficAnalyticFilter;
use App\Models\Trafic;
use App\Models\User;
use App\Repositories\Trafic\TraficRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Analytic\Trafic\AnalyticTrafic;
use App\Services\Analytic\Trafic\TargetTotalTraficAnalytic;
use App\Services\Analytic\Trafic\TargetTraficAnalytic;
use App\Services\Analytic\Trafic\TotalTraficAnalytic;
use App\Services\Analytic\Trafic\DeleteTraficAnalytic;
use App\Services\Analytic\Trafic\GeneralTraficAnalytic;
use App\Services\Analytic\Trafic\AuthorTraficAnalytic;
use App\Services\Analytic\Trafic\PersonalTraficAnalytic;
use App\Services\Analytic\Trafic\ChanelTraficAnalytic;
use App\Services\Analytic\Trafic\TargetPlanTraficAnalytic;


class TraficController extends Controller
{
    public function __invoke(Request $request, TraficRepository $repo, AnalyticTrafic $analytic)
    {
        return response()->json([

            'data' => [
                'target_total'  => $analytic->analytics($request->all(), new TargetTotalTraficAnalytic()),
                
                'target'    	=> $analytic->analytics($request->all(), new TargetTraficAnalytic()),
                
                'total'     	=> $analytic->analytics($request->all(), new TotalTraficAnalytic()),

                'deleted'   	=> $analytic->analytics($request->all(), new DeleteTraficAnalytic()),

                'general'   	=> $analytic->analytics($request->all(), new GeneralTraficAnalytic()),
                
                'author'    	=> $analytic->analytics($request->all(), new AuthorTraficAnalytic()),
                
                'personal'  	=> $analytic->analytics($request->all(), new PersonalTraficAnalytic()),
                
                'chanel'    	=> $analytic->analytics($request->all(), new ChanelTraficAnalytic()),                              
            ],

            'success' => 1,
        ]);
    }
}
