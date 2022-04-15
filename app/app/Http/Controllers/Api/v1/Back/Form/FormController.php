<?php

namespace App\Http\Controllers\Api\v1\Back\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function store(Request $request)
    {
        dd($request->all());
    }
}
