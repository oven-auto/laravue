<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Repositories\Worksheet\WorksheetRepository;
use App\Services\Analytic\AnalyticWorksheet;
use Illuminate\Http\Request;

class WorksheetController extends Controller
{
    public function __invoke(Request $request, WorksheetRepository $repo, AnalyticWorksheet $analytic)
    {
        $structureIds = Structure::select('structures.*')
            ->leftJoin('company_structures', 'company_structures.structure_id', 'structures.id')
            ->whereIn('company_structures.id', $request->structure_ids)
            ->pluck('id')
            ->toArray();

        $request->request->remove('structure_ids');

        $request->merge(['structure_ids' => $structureIds]);

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
                'work'      => \App\Services\Analytic\WorksheetAuthor::getCount($nonIntervalArray),
            ],
            'success' => 1,
        ]);
    }
}
