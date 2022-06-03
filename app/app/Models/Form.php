<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\SortInterface;

class Form extends Model implements SortInterface
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function event()
    {
        return $this->hasOne(\App\Models\FormEvent::class)->withDefault();
    }

    public function recipients()
    {
        return $this->belongsToMany(\App\Models\User::class, 'form_recipients', 'form_id');
    }

    public function bodies()
    {
        return $this->belongsToMany(\App\Models\FormControll::class, 'form_bodies', 'form_id');
    }
}
