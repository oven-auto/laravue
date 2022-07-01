<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageTool extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    //protected $with = ['toolable'];

    public function toolable()
    {
        return $this->morphTo();
    }
}
