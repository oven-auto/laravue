<?php

namespace App\Http\Controllers\Api\v1\BreadCrumbs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientBreadCrumbsController extends Controller
{
    public function title(Request $request)
    {
        $res = [];

        if($request->has('lastname'))
            $res['lastname'] = $request->get('lastname');

        if($request->has('firstname'))
            $res['firstname'] = $request->get('firstname');

        if($request->has('fathername'))
            $res['fathername'] = $request->get('fathername');

        if($request->has('email'))
            $res['email'] = $request->get('email');

        if($request->has('phone'))
            $res['phone'] = $request->get('phone');

        return response()->json([
            'data' => $res
        ]);
    }
}
