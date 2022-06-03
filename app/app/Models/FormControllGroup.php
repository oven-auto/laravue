<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormControllGroup extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function elements()
    {
        return $this->hasMany(\App\Models\FormControll::class);
    }
}
