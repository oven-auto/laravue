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
        //header('Content-Type: application/pdf');
        //header("Content-Disposition", "inline; filename=trafic.pdf");
        //header('Content-Disposition: inline; filename="trafic.pdf"');
        $pdf = PDF::loadView('pdf.trafic', [
            'trafic' =>$trafic
        ]);
        // return response()->json([
        //     'data' => $pdf
        // ]);
        // header('Content-type: application/pdf');
        // header('Content-Description: File Transfer');
        // header('Content-Disposition: attachment; filename="demo.pdf"');
        // header('Content-Transfer-Encoding: binary');

        //return $pdf->download('trafic.pdf');

        return $pdf->stream('trafic.pdf', ['Attachment' => 0]);
    }
}
