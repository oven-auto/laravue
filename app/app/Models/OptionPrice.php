<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionPrice extends Model
{
    use HasFactory;

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



    // public function current()
    // {
    //     return $this->hasOne(\App\Models\OptionCurrentPrice::class, 'id', 'id');
    // }
}
