<?php

namespace App\Models\Interfaces;

use App\Models\PageTool;

interface ToolInterface
{
    public function getTool(PageTool $pageTool);
}
