<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplectationCurrentPrice extends Model
{
    use HasFactory;

    public $dates = ['begin_at'];
}
