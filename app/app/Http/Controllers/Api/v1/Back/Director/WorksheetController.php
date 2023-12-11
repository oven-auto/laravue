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


        $nonIntervalArray = $request->except([
                'interval_begin', 'interval_end',
                'second_interval_begin', 'second_interval_end',
                'third_interval_begin', 'third_interval_end',
        ]);

        return response()->json([
            'data' => [
                'author'    => \App\Services\Analytic\WorksheetAuthor::getCountAnalyticByAuthor($nonIntervalArray),
                'created'   => $analytic->fasade($request->all(), new \App\Services\Analytic\CreatedWorksheetAnalytic()),
                'closed'    => $analytic->fasade($request->all(), new \App\Services\Analytic\ClosedWorksheetAnalytic()),
                'results'   => $analytic->fasade($request->all(), new \App\Services\Analytic\ResultWorksheetAnalytic()),
                'work'      => $repo->workingCount($nonIntervalArray)
            ],
            'success' => 1,
        ]);
    }
}
