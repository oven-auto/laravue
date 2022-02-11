<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'brand_color', 'font_color'];

    public $timestamps = false;

    public function getIconDateAttribute()
    {
    	return asset('storage'.$this->icon) . '?' . date('dmyhm');
    }
}
