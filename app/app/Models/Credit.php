<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function marks()
    {
        return $this->belongsToMany(\App\Models\Mark::class, 'credit_marks', 'credit_id');
    }
}
