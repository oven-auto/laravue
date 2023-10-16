<?php

namespace App\Http\Controllers\Api\v1\Back\Trafic;

use App\Http\Controllers\Controller;
use App\Http\Resources\Trafic\LinkCollection;
use App\Http\Resources\Trafic\LinkResource;
use App\Models\TraficLink;
use App\Services\GetShortCutFromURL\GetShortCutFromURL;
use Illuminate\Http\Request;

class TraficLinkController extends Controller
{
    public function index(Request $request)
    {
        $links = TraficLink::with('author')
            ->where('trafic_id', $request->trafic_id)
            ->get();

        return new LinkCollection($links);
    }

    public function store(TraficLink $link, Request $request)
    {
        $link->fill([
            'author_id' => auth()->user()->id,
            'text' => $request->get('text'),
            'icon' => GetShortCutFromURL::get($request->get('text')),
            'trafic_id' => $request->get('trafic_id'),
        ])->save();

        return response()->json([
            'data' => new LinkResource($link),
            'success' => 1,
            'message' => 'Ссылка добавлена'
        ]);
    }

    public function update(TraficLink $link, Request $request)
    {
        $link->fill([
            'author_id' => auth()->user()->id,
            'text' => $request->get('text'),
            'icon' => GetShortCutFromURL::get($request->get('text'),'shortcut','href'),
        ])->save();

        return response()->json([
            'data' => new LinkResource($link),
            'success' => 1,
            'message' => 'Ссылка изменена'
        ]);
    }

    public function delete(TraficLink $link)
    {
        $link->delete();

        return response()->json([
            'success' => 1,
            'message' => 'Ссылка удалена'
        ]);
    }

    public function count(Request $request)
    {
        $count = TraficLink::where('trafic_id', $request->trafic_id)->count();

        return response()->json([
            'count' => $count,
            'success' => 1,
        ]);
    }
}