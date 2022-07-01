<?php

namespace App\Http\Controllers\Api\v1\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\PageText;
use DB;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::with('section');
        $pages = $query->get();
        if($pages->count())
            return response()->json([
                'status' => 1,
                'data' => $pages,
                'count' => $pages->count(),
                'message' => 'Найдено '.$pages->count().' страниц'
            ]);
        return response()->json([
            'status' => 0,
            'message' => 'Не нашлось ни одной страницы'
        ]);
    }

    public function edit($page)
    {
        $page = Page::with('tools.toolable')->find($page);

        $data['section_page_id'] = $page->section_page_id;
        $data['title'] = $page->title;
        $data['status'] = $page->status;
        $data['id'] = $page->id;
        foreach ($page->tools as $item) {
            $data['tools'][] = $item->toolable->getTool($item);
        }
        return response()->json([
            'status' => 1,
            'data' => $data,
        ]);
    }

    public function store(Page $page, Request $request)
    {
        $page->fill($request->except('tools'))->save();

        $mas = [];
        foreach($request->get('tools') as $item) {
            if($item['type'] == 'pagetext')
                $mas[] =[
                    'toolable_id' => PageText::create(['text' => $item['value']])->id,
                    'toolable_type' => 'App\Models\PageText',
                    'sort' => $item['sort']
                ];
            elseif($item['type'] == 'form')
                $mas[] =[
                    'toolable_id' => $item['value'],
                    'toolable_type' => 'App\Models\Form',
                    'sort' => $item['sort']
                ];
        }
        $page->tools()->delete();
        $page->tools()->createMany($mas);

        return response()->json([
            'status' => 1,
            'data' => $page,
            'message' => 'Страница создана'
        ]);
    }

    public function update(Page $page, Request $request)
    {
        $page->fill($request->except('tools'))->save();

        $mas = [];
        foreach($request->get('tools') as $item) {
            if($item['type'] == 'pagetext')
                $mas[] =[
                    'toolable_id' => PageText::create(['text' => $item['value']])->id,
                    'toolable_type' => 'App\Models\PageText',
                    'sort' => $item['sort']
                ];
            elseif($item['type'] == 'form')
                $mas[] =[
                    'toolable_id' => $item['value'],
                    'toolable_type' => 'App\Models\Form',
                    'sort' => $item['sort']
                ];
        }
        $page->tools()->delete();
        $page->tools()->createMany($mas);


        return response()->json([
            'status' => 1,
            'data' => $page,
            'message' => 'Страница изменена'
        ]);
    }

    public function destroy(Page $page)
    {
        $name = $page->title;
        $page->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Страница '.$name.' удалена'
        ]);
    }
}
