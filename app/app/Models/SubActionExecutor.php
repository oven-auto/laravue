<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubActionExecutor extends Model
{
    use HasFactory;

    public $timestamp = false;

    protected $guarded = [];
}
