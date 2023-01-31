<?php

namespace App\Http\Controllers\Api\v1\Listing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ChanelController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => \App\Models\TraficChanel::select(['id','name'])
                ->where('trafic_chanels.parent','<>','0')
                ->orWhere(DB::raw('(select count(zz.id) from trafic_chanels as zz where zz.parent = trafic_chanels.id)'),'<','1')
                ->orderBy('trafic_chanels.parent')
                ->orderBy('trafic_chanels.name')
                ->get(),
            'success' => 1,
        ]);
    }
}
