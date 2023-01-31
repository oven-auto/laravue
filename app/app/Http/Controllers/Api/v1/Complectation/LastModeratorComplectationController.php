<?php

namespace App\Http\Controllers\Api\v1\Complectation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complectation;

class LastModeratorComplectationController extends Controller
{
    public function index(Complectation $complectation)
    {
        $complectation->lastmoderator;
        $complectation->lastmoderator->user;
        return response()->json([
            'data' => $complectation->lastmoderator
        ]);
    }
}
