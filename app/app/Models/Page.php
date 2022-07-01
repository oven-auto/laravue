<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function section()
    {
        return $this->hasOne(\App\Models\SectionPage::class, 'id', 'section_page_id')->withDefault();
    }

    public function tools()
    {
        return $this->hasMany(\App\Models\PageTool::class);
    }
}
