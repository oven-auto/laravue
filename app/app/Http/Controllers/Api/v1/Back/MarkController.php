<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;
use Storage;

class MarkController extends Controller
{
    public function index(Request $request)
    {
        $query = Mark::with(['icon', 'bodywork', 'brand']);

        if($request->has('brand_id'))
            $query->where('brand_id', $request->get('brand_id'));

        $marks = $query->orderBy('status','DESC')->orderBy('sort')->get();
        foreach($marks as $item) {
            $item->icon->image =  asset('storage'.$item->icon->image);
        }

        if($marks->count())
            return response()->json([
                'status' => 1,
                'data' => $marks,
                'count' => $marks->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одной модели'
        ]);
    }

    public function edit(Mark $mark, Request $request, \App\Repositories\MarkRepository $service)
    {
        $service->loadFullData($mark);

        return response()->json([
            'status' => 1,
            'mark' => $mark->toArray()
        ]);
    }

    public function store(Mark $mark, Request $request, \App\Repositories\MarkRepository $service)
    {
        $data = $request->all();
        $data['sort'] = Mark::max('sort')+1;
        $service->saveMark($mark, $data);

        return response()->json([
            'status' => 1,
            'mark' => $mark,
            'message' => 'Модель создана'
        ]);
    }

    public function update(Mark $mark, Request $request, \App\Repositories\MarkRepository $service)
    {
        $service->saveMark($mark, $request->all());

        return response()->json([
            'status' => 1,
            'mark' => $mark,
            'message' => 'Модель изменена'
        ]);
    }
}
