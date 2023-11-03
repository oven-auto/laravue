<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Models\Worksheet;
use App\Models\WorksheetLink;
use App\Services\GetShortCutFromURL\GetShortCutFromURL;
use Illuminate\Http\Request;

class WorksheetLinkController extends Controller
{
    public function index(Worksheet $worksheet, Request $request)
    {
        $links = $worksheet->links;

        return response()->json([
            'data' => $links->map(function($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->url,
                    'icon' => $item->icon,
                    'author' => $item->author->cut_name,
                    'created_at' => $item->created_at->format('d.m.Y (H:i)')
                ];
            }),
            'success' => 1,
        ]);
    }

    public function store(Worksheet $worksheet, Request $request)
    {
        $link = $worksheet->links()->create([
            //'worksheet_id' => $request->worksheet_id,
            'author_id' => auth()->user()->id,
            'icon' => GetShortCutFromURL::get($request->url),
            'url' => $request->url,
        ]);

        return response()->json([
            'data' => [
                'id' => $link->id,
                'text' => $link->url,
                'icon' => $link->icon,
                'author' => $link->author->cut_name,
                'created_at' => $link->created_at->format('d.m.Y (H:i)')
            ],
            'success' => 1,
            'message' => 'Ссылка создана'
        ]);
    }

    public function delete(WorksheetLink $worksheetLink)
    {
        $worksheetLink->delete();

        return response()->json([
            'message' => 'Ссылка удалена',
            'success' => 1,
        ]);
    }
}
