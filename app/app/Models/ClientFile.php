<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function author()
    {
        return $this->hasOne(\App\Models\User::class,'id','author_id')->withDefault();
    }

    public function client()
    {
        return $this->hasOne(\App\Models\Client::class,'id','client_id')->withDefault();
    }

    public function getDateAttribute()
    {
        return $this->created_at->format('d.m.Y (H:i)');
    }
}
