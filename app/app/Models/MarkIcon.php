<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkIcon extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function getImageDateAttribute()
    {
    	return asset('storage'.$this->image) . '?' . date('dmyhm');
    }
}
