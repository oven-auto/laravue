<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use WebUrl;

class MarkIcon extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getImageDateAttribute()
    {
        return WebUrl::plugCarOrImage($this->image);
    }
}
