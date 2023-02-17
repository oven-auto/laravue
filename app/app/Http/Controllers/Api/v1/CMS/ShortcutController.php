<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shortcut;

class ShortcutController extends Controller
{
    public function index(Request $request)
    {
        $query = Shortcut::select('*');
        if($request->has('brand_id'))
            $query->where('brand_id',$request->get('brand_id'));
        $shortcuts = $query->get();
        if($shortcuts->count())
            return response()->json([
                'status' => 1,
                'data' => $shortcuts,
                'count' => $shortcuts->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одного ярлыка'
        ]);
    }

    public function edit(Shortcut $shortcut)
    {
        return response()->json([
            'status' => 1,
            'data' => $shortcut
        ]);
    }

    public function store(Shortcut $shortcut, Request $request)
    {
        $shortcut->fill($request->all())->save();

        return response()->json([
            'status' => 1,
            'data' => $shortcut,
            'message' => 'Ярлык создан'
        ]);
    }

    public function update(Shortcut $shortcut, Request $request)
    {
        $shortcut->fill($request->all())->save();

        return response()->json([
            'status' => 1,
            'data' => $shortcut,
            'message' => 'Ярлык изменен'
        ]);
    }
}
