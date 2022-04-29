<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SectionPage;
use App\Models\Page;

class PageController extends Controller
{
    public function sections(Request $request) 
    {
    	$sections = [];

    	if($request->has('brand_id')) {
    		$sections = SectionPage::with([
                'pages:id,title,section_page_id',
                'forms'])
            ->where('brand_id', $request
            ->get('brand_id'))->get();
    	}

    	return response()->json([
    		'data' => $sections,
    		'status' => count($sections) ? 1 : 0
    	]);
    }

    public function page(Request $request) 
    {
    	$page = [];

    	if($request->has('page_id'))
    		$page = Page::with('section')->find($request->get('page_id'));

    	return response()->json([
    		'data' => $page,
    		'status' => isset($page->id) ? 1 : 0
    	]);
    }
}
