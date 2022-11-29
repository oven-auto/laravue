<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Createable;

class BodyWork extends Model
{
    use HasFactory, Createable;

    protected $fillable = ['name', 'sort'];

    public $timestamps = false;
}
