<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\ToolInterface;

class PageText extends Model implements ToolInterface
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function tool()
    {
        return $this->morphMany(\App\Models\PageTool::class, 'toolable');
    }

    public function getTool($pageTool)
    {
        return [
            'type' => 'pagetext',
            'value' => $this->text,
            'sort' => $pageTool->sort
        ];
    }
}
