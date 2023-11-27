<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use App\Services\Comment\Comment;
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



        //Скачивание
        //return $pdf->download('trafic.pdf');

        //Открыть в браузере
        return $pdf->stream('trafic.pdf', ['Attachment' => 0]);
    }
}
