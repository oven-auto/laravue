<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Http\Controllers\Controller;
use App\Http\Filters\TraficAnalyticFilter;
use App\Http\Filters\WorksheetAnalyticFilter;

use App\Http\Filters\TraficFilter;
use App\Http\Filters\WorksheetFilter;


use App\Models\Trafic;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    public function __invoke(Request $request)
    {
        $nonIntervalArray = $request->except([
                'interval_begin', 'interval_end',
                'second_interval_begin', 'second_interval_end',
                'third_interval_begin', 'third_interval_end',
        ]);
        //dd($nonIntervalArray);
        $filterTrafic       = app()->make(TraficAnalyticFilter::class, ['queryParams' => array_filter($nonIntervalArray)]);
        $filterWorkSheet    = app()->make(WorksheetAnalyticFilter::class, ['queryParams' => array_filter($nonIntervalArray)]);

        $counter = [
            'trafic' => [
                'count' => Trafic::query()->whereIn('trafics.trafic_status_id', [1,2])->filter($filterTrafic)->count(),
                'ids' => Trafic::select('trafics.id')->whereIn('trafics.trafic_status_id', [1,2])->filter($filterTrafic)->pluck('id')
            ],

            'events' => [
                'count' => Worksheet::select([\DB::raw('COUNT(worksheets.id) as count'),])
                    ->where('worksheets.status_id','work')
                    ->where('worksheet_actions.end_at','<', now())
                    ->filter($filterWorkSheet)
                    ->groupBy('worksheets.id')
                    ->get()
                    ->count(),
                'ids' => Worksheet::select([\DB::raw('worksheets.id'),])
                    ->where('worksheets.status_id','work')
                    ->where('worksheet_actions.end_at','<', now())
                    ->filter($filterWorkSheet)
                    ->groupBy('worksheets.id')
                    ->pluck('id'),
            ],

            'worksheet' => [
                'count' => Worksheet::select([\DB::raw('COUNT(worksheets.id) as count'),])
                    ->where('worksheets.status_id','check')
                    ->filter($filterWorkSheet)
                    ->groupBy('worksheets.id')
                    ->get()
                    ->count(),
                'ids' => Worksheet::select(['worksheets.id'])
                ->where('worksheets.status_id','check')
                ->filter($filterWorkSheet)
                ->groupBy('worksheets.id')
                ->pluck('id'),
            ],
        ];

        return response()->json([
            'data' => $counter,
            'success' => 1
        ]);
    }
}
