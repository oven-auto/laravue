<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorksheetAction extends Model
{
    use HasFactory;

    public $dates = [
        'begin_at', 'end_at', 'created_at', 'updated_at'
    ];

    protected $guarded = [];

    public function getBeginDateAttribute()
    {
        if($this->begin_at)
            return $this->begin_at->format('d.m.Y (H:i)');
        return '';
    }

    public function getEndDateAttribute()
    {
        if($this->end_at)
            return $this->end_at->format('d.m.Y (H:i)');
        return '0';
    }

    public function task()
    {
        return $this->hasOne(\App\Models\Task::class,'id','task_id')->withDefault();
    }

    public function isWorking()
    {
        return $this->status == 'work' ? true : false;
    }

    public function isWaiting()
    {
        return $this->end_at > now() ? true : false;
    }

    public function last_comment()
    {
        return $this->hasOne(\App\Models\WorksheetActionComment::class,'action_id', 'id')->orderBy('id','DESC')->withDefault();
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\WorksheetActionComment::class,'action_id', 'id')->with('author');
    }

    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id')->withDefault();
    }

    public function statusMsg()
    {
        switch ($this->status){
            case 'work':
                return '';
            case 'confirm':
                return 'Подтверждено';
            case 'abort':
                return 'Отменено';
            default:
                return '';
        }
    }

    //public function

    public function worksheet()
    {
        return $this->hasOne(\App\Models\Worksheet::class, 'id', 'worksheet_id')->withDefault();
    }
}
