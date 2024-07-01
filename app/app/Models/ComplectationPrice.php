<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplectationPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public $dates = ['created_at', 'updated_at', 'begin_at', 'deleted_at'];



    /**
     * RELATIONs
     */

    /**
     * AUTHOR
     */
    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id')->withDefault();
    }
}
