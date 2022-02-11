<?php

namespace App\Http\Controllers\Api\v1\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mark;
use App\Repositories\MarkRepository;
use App\Http\Resources\Mark\MarkListCollection;
use App\Http\Resources\Mark\MarkItemResource;
use App\Http\Resources\Mark\MarkNameCollection;

class MarkController extends Controller
{
    private $service;

    public function __construct(MarkRepository $service)
    {
        $this->service = $service;
    }

    public function list()
    {
    	$marks = $this->service->getMarkTabList();
    	return new MarkListCollection($marks);
    }

    public function get($slug)
    {
        $mark = $this->service->getMarkBySlug($slug);
        return new MarkItemResource($mark);
    }

    public function getMarksName()
    {
        $marks = $this->service->getMarksName();
        return new MarkNameCollection($marks);
    }
}
