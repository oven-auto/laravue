<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TraficChanel;
use App\Http\Resources\Trafic\TraficListCollection;

class TraficChanelController extends Controller
{
    public function index()
    {
        $chanels = TraficChanel::with('childrens')->where('parent',0)->orderBy('sort')->get();

        return new TraficListCollection($chanels);
    }
}
