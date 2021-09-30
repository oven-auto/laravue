<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionPage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function pages()
    {
        return $this->hasMany(App\Models\Page::class, 'section_page_id', 'id');
    }

    public function brand()
    {
        return $this->hasOne(App\Models\Brand::class, 'id', 'brand_id')->withDefault();
    }
}
