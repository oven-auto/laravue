<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Createable;

class CountryFactory extends Model
{
    use HasFactory, Createable;

    protected $guarded = [];

    public $timestamps = false;
}
