<?php

namespace App\Http\Controllers\Api\v1\Services\Html\Select;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserSelectController extends Controller
{
    public function index()
    {
        $users = User::get();
        return response()->json([
            'data'=>$users,
            'status'=>$users->count() ? 1 : 0,
            'count' => $users->count()
        ]);
    }
}
