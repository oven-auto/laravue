<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;

class TraficPDFController extends Controller
{
    public function __invoke()
    {
        $data = ['id' => 1];
        $pdf = PDF::loadView('pdf.trafic', $data);
        return $pdf->download('demo.pdf');
    }
}
