<?php

namespace App\Http\Controllers\Api\v1\Back\Worksheet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientWorksheetListController extends Controller
{
    public function __invoke(\App\Models\Client $client)
    {
        $data = $client->worksheets;
        return new \App\Http\Resources\Client\WorksheetListCollection($data);
    }
}
