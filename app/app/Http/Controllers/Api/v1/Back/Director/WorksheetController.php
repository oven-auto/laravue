<?php

namespace App\Http\Controllers\Api\v1\Back\Director;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Repositories\Worksheet\WorksheetRepository;
use App\Services\Analytic\Worksheet\AnalyticWorksheet;
use Illuminate\Http\Request;
use App\Services\Analytic\Worksheet\WorksheetAuthor;
use App\Services\Analytic\Worksheet\ResultWorksheetAnalytic;
use App\Services\Analytic\Worksheet\ClosedWorksheetAnalytic;
use App\Services\Analytic\Worksheet\CreatedWorksheetAnalytic;

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
                'author'    => WorksheetAuthor::getCountAnalyticByAuthor($nonIntervalArray),
                'created'   => $analytic->fasade($request->all(), new CreatedWorksheetAnalytic()),
                'closed'    => $analytic->fasade($request->all(), new ClosedWorksheetAnalytic()),
                'results'   => $analytic->fasade($request->all(), new ResultWorksheetAnalytic()),
                'work'      => WorksheetAuthor::getCount($nonIntervalArray),
            ],
            'success' => 1,
        ]);
    }
}
