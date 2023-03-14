<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use WebUrl;

class MarkColor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function color()
    {
        return $this->hasOne(\App\Models\Color::class, 'id', 'color_id')->withDefault();
    }

    public function getImageDateAttribute()
    {
    	return WebUrl::make_link($this->image);
    }
}
