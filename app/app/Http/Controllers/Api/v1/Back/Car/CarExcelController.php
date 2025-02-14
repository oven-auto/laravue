<?php

namespace App\Http\Controllers\Api\v1\Back\Car;

use App\Exports\CarExport;
use App\Http\Controllers\Controller;
use App\Repositories\Car\Car\CarRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CarExcelController extends Controller
{
    private $repo;

    public function __construct(CarRepository $repo)
    {
        $this->repo = $repo;    
    }



    public function index(Request $request)
    {
        $cars = $this->repo->get($request->all());

        return view('export.cars', [
            'trafics' => $cars
        ]);
        
        $export = (new CarExport($cars));

        return Excel::download($export, 'cars.xlsx');
    }
}
