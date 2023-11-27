<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use App\Http\Resources\Worksheet\Link\LinkCollection;
use App\Http\Resources\Worksheet\Link\LinkResource;
use App\Models\Worksheet;
use App\Models\WorksheetLink;
use App\Services\GetShortCutFromURL\GetShortCutFromURL;
use Illuminate\Http\Request;
use App\Services\Comment\Comment;

class WorksheetLinkController extends Controller
{
    public function index(Worksheet $worksheet, Request $request)
    {
        $links = $worksheet->links;

        return new LinkCollection($links);
    }

    public function store(Worksheet $worksheet, Request $request)
    {
        $link = $worksheet->links()->create([
            'author_id' => auth()->user()->id,
            'icon' => GetShortCutFromURL::get($request->url),
            'url' => $request->url,
        ]);

        Comment::add($link, 'create');

        return (new LinkResource($link))->additional([
            'success' => 1, 'message' => 'Ссылка добавлена'
        ]);
    }

    public function delete(WorksheetLink $worksheetLink)
    {
        Comment::add($worksheetLink, 'delete');

        $worksheetLink->delete();

        return response()->json([
            'message' => 'Ссылка удалена',
            'success' => 1,
        ]);
    }
}
