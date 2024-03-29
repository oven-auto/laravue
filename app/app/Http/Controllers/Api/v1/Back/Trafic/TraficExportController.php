<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Excel;
use App\Exports\TraficExport;
use App\Repositories\Trafic\TraficRepository;
use Auth;

class TraficExportController extends Controller
{
    public function export(Request $request, TraficRepository $service)
    {
        $data = $service->export($request->all());
        $export = TraficExport::setData($data);
        return Excel::download($export, 'trafics.xlsx');
    }
}
