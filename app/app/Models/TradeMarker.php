<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMarker extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }
}
