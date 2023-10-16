<?php

namespace App\Http\Controllers\Api\v1\Back\TaskList;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskList\TraficListCollection;
use App\Repositories\Trafic\TraficRepository;
use Illuminate\Http\Request;

class TraficListController extends Controller
{
    public function __invoke(TraficRepository $repo, Request $request)
    {
        // if(!$request->has('show'))
        //     $request->merge(['show' => 'opening']);

        $trafics = $repo->get($request->all());

        return (new TraficListCollection($trafics))
            ->additional([
                'request' => $request->all(),
            ]);
    }
}














        // $date = new \Carbon\Carbon($request->control_date);
        // $date = $date->format('Y-m-d');

        // $showClosing = $request->has('closing') ? true : false;

        // $trafics = \App\Models\Trafic::query()
        //     ->with([
        //         'needs', 'sex', 'zone', 'chanel.myparent',
        //         'salon', 'structure', 'appeal', 'manager',
        //         'author', 'person'
        //     ])
        //     ->where(function($query) use ($date, $showClosing){
        //         if(now()>=$date)
        //             //Условие что за текющую дату либо ранее, если сегодня меньше либо равно указанной даты
        //             $query->whereDate('trafics.begin_at', '<=', $date);
        //         else
        //             //Условие только за указанную дату
        //             $query->whereDate('trafics.begin_at', '=', $date);

        //         $query->where(function($subQuery) {
        //             $subQuery->where('author_id', auth()->user()->id);
        //             $subQuery->orWhere('manager_id', auth()->user()->id);
        //         });

        //         if($showClosing)
        //             $query->whereNotIn('trafics.trafic_status_id', [1,2]);
        //         else
        //             $query->where('trafics.trafic_status_id', 2)->orWhere('trafics.trafic_status_id', 1);
        //     })
        //     ->orderBy('begin_at')
        //     ->get();
