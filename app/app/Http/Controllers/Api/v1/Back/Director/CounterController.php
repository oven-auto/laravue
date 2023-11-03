<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Http\Controllers\Controller;
use App\Http\Filters\TraficFilter;
use App\Http\Filters\WorksheetFilter;
use App\Models\Trafic;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    public function __invoke(Request $request)
    {
        if(!$request->has('appeal_ids'))
            $request->merge(['appeal_ids' => auth()->user()->appeals->map(fn($item) => [
                $item->id
            ])->toArray()]);

        $data['appeal_ids'] = auth()->user()->appeals->map(fn($item) => [
            $item->id
        ])->toArray();

        $filterTrafic       = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);
        $filterWorkSheet    = app()->make(WorksheetFilter::class, ['queryParams' => array_filter($data)]);

        $counter = [
            'trafic' => [
                'count' => Trafic::query()->whereIn('trafics.trafic_status_id', [1,2])->filter($filterTrafic)->count(),
                'ids' => Trafic::select('trafics.id')->whereIn('trafics.trafic_status_id', [1,2])->filter($filterTrafic)->pluck('id')
            ],
            'worksheet' => [
                'count' => Worksheet::query()->where('worksheets.status_id','check')->filter($filterWorkSheet)->count(),
                'ids' => Worksheet::select('worksheets.id')->where('worksheets.status_id','check')->filter($filterWorkSheet)->pluck('id')
            ],
            'events' => [
                'count' => Worksheet::query()->where('worksheets.status_id','work')
                    ->leftJoin('worksheet_actions', function($join) {
                        $join->on('worksheet_actions.worksheet_id','worksheets.id');
                    })
                    ->where(
                        'worksheet_actions.id',
                        \DB::raw('(SELECT max(SWA.id) FROM worksheet_actions as SWA WHERE SWA.worksheet_id = worksheets.id)')
                    )
                    ->where('worksheet_actions.end_at','<', now())->count(),

                'ids' => Worksheet::select('worksheets.id')->where('worksheets.status_id','work')
                    ->leftJoin('worksheet_actions', function($join) {
                        $join->on('worksheet_actions.worksheet_id','worksheets.id');
                    })
                    ->where(
                        'worksheet_actions.id',
                        \DB::raw('(SELECT max(SWA.id) FROM worksheet_actions as SWA WHERE SWA.worksheet_id = worksheets.id)')
                    )
                    ->where('worksheet_actions.end_at','<', now())->pluck('worksheets.id'),
            ]
        ];

        return response()->json([
            'data' => $counter,
            'success' => 1
        ]);
    }
}
