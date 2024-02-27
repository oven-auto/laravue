<?php

namespace App\Http\Controllers\Api\v1\Back\TaskList;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskList\WorksheetListCollection;
use App\Repositories\Worksheet\WorksheetRepository;
use Illuminate\Http\Request;

class WorksheetListController extends Controller
{
    public function __invoke(WorksheetRepository $repo, Request $request)
    {
        $worksheets = $repo->getWorksheetsForTaskList($request->all());

        $subAction = $repo->getSubActionForTaskList($request->all());

        $collect = collect(array_merge($subAction, $worksheets));

        $merged = $collect->sortByDesc('sort')->values();

        return (new WorksheetListCollection($merged->all()))
            ->additional(['test' => ('11.12.2023' > '02.02.2024')]);
    }
}


















//$date = new \Carbon\Carbon($request->control_date);
        //$date = $date->format('Y-m-d');

        //$showClosing = $request->has('closing') ? true : false;

        // $query = \App\Models\Worksheet::query()
        //     ->select('worksheets.*')
        //     ->with(['last_action.task','author', 'executors','company','structure', 'appeal','client.type'])
        //     //Цепляю таблицу исполнителей
        //     ->leftJoin('worksheet_executors', 'worksheet_executors.worksheet_id', 'worksheets.id')
        //     //Цепляю таблицу действий
        //     ->leftJoin('worksheet_actions', 'worksheet_actions.worksheet_id', 'worksheets.id')
        //     //Условие брать самое последнее действие
        //     ->where('worksheet_actions.id', \DB::raw('(SELECT max(SWA.id) FROM worksheet_actions as SWA WHERE SWA.worksheet_id = worksheets.id)'));

        //     if($showClosing)
        //         //Условие что все РЛ открыты (в закрыт с продажей или закрыт с отказом)
        //         $query->whereIn('worksheets.status_id', ['close','complete']);
        //     else
        //         //Условие что все РЛ открыты (в работе или на проверке)
        //         $query->whereIn('worksheets.status_id', ['work','check']);

        //     $worksheets = $query->where(function($query) use ($date){
        //         if(now()>=$date)
        //             //Условие что за текющую дату либо ранее
        //             $query->whereDate('worksheet_actions.begin_at', '<=', $date);
        //         else
        //             $query->whereDate('worksheet_actions.begin_at', '=', $date);

        //         $query->where(function($subQuery) {
        //             //Условие что исполнитель это пользователь
        //             $subQuery->where('worksheet_executors.user_id', auth()->user()->id);
        //             //Условие что исполнитель это автор
        //             //$subQuery->orWhere('worksheets.author_id', auth()->user()->id);
        //         });
        //     })
        //     ->groupBy('worksheets.id')
        //     ->groupBy('worksheet_actions.begin_at')
        //     //Сортировка по началу действия
        //     ->orderBy('worksheet_actions.begin_at')
        //     ->get();
