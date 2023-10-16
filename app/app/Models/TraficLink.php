<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraficLink extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id')->withDefault();
    }

    public function trafic()
    {
        return $this->hasOne(\App\Models\Trafic::class, 'id', 'trafic_id')->withDefault();
    }
}
