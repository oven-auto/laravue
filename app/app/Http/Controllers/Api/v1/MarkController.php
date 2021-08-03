<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;

class MarkController extends Controller
{
    public function index()
    {
        echo 'index';
    }

    public function edit(Mark $mark, Request $request)
    {
        echo 'edit';
    }

    public function store(Mark $mark, Request $request)
    {
        dd($request->all());
    }

    public function update(Mark $mark, Request $request)
    {
        echo 'update';
    }
}
