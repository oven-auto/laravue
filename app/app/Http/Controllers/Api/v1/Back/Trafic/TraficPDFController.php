<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use App\Models\Trafic;

class TraficPDFController extends Controller
{
    public function __invoke(Trafic $trafic)
    {
        $pdf = PDF::loadView('pdf.trafic', [
            'trafic' =>$trafic
        ]);
        return $pdf->download('demo.pdf');
    }
}
