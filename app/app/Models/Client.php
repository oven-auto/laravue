<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Createable;
use App\Models\Traits\Filterable;

class Client extends Model
{
    use HasFactory, Createable, Filterable;

    protected $guarded = [];
}
