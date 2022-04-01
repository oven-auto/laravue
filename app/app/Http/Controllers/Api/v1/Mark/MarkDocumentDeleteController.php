<?php

namespace App\Http\Controllers\Api\v1\Mark;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarkDocument;
use Storage;

class MarkDocumentDeleteController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->except('_method');
        $document = MarkDocument::where('mark_id',$data['mark_id'])->first();

        $type = $data['type'];

        if(isset($document->$type)) {
            Storage::disk('public')->delete($document->$type);
            $document->$type = '';
            $document->save();
        }

        return response()->json([
            'status' => $document,
        ]);
    }
}
