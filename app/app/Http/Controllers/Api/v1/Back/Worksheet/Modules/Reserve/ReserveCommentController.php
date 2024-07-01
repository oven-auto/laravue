<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet\Modules\Reserve;

use App\Http\Controllers\Controller;
use App\Models\WsmReserveComment;
use Illuminate\Http\Request;

class ReserveCommentController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'reserve_id' => 'required',
        ]);

        $comments = WsmReserveComment::where('reserve_id', $validated['reserve_id'])->get();

        return response()->json([
            'data' => $comments->map(fn ($item) => [
                'text' => $item->text,
                'id' => $item->id,
                'author' => $item->author->cut_name,
                'created_at' => $item->created_at->format('d.m.Y (H:i)'),
            ]),
            'success' => 1,
        ]);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'reserve_id' => 'required',
            'text' => 'required'
        ]);

        $comment = WsmReserveComment::create(array_merge(
            $validated,
            ['author_id' => auth()->user()->id],
        ));

        return response()->json([
            'message' => 'Comment Appended',
            'success' => 1,
        ]);
    }
}
