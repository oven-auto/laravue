<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::select('*');
        $pages = $query->get();
        if($pages->count())
            return response()->json([
                'status' => 1,
                'data' => $pages,
                'count' => $pages->count()
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одной страницы'
        ]);
    }

    public function edit(Page $page)
    {
        return response()->json([
            'status' => 1,
            'data' => $page
        ]);
    }

    public function store(Page $page, Request $request)
    {
        $page->fill($request->all())->save();

        return response()->json([
            'status' => 1,
            'data' => $page,
            'message' => 'Страница создана'
        ]);
    }

    public function update(Page $page, Request $request)
    {
        $page->fill($request->all())->save();

        return response()->json([
            'status' => 1,
            'data' => $page,
            'message' => 'Страница изменена'
        ]);
    }
}
