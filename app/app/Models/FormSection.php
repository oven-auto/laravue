<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSection extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function form()
    {
        return $this->hasMany(\App\Models\Form::class)->orderBy('sort');
    }

    public function menuform()
    {
    	return $this->hasMany(\App\Models\Form::class)->where('menu_status',1)->orderBy('sort');
    }
}
