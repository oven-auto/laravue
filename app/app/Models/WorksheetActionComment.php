<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorksheetActionComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function author()
    {
        return $this->hasOne(\App\Models\User::class,'id','author_id')->withDefault();
    }

    public function action()
    {
        return $this->hasOne(\App\Models\WorksheetAction::class, 'id', 'action_id')->withDefault();
    }
}
