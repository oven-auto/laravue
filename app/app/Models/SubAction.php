<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\CommentInterface;

class SubAction extends Model implements CommentInterface
{
    use HasFactory;

    protected $guarded = [];

    private const STATUSES = [
        1 => 'В работе',
        2 => 'На проверке',
        3 => 'Закрыто'
    ];

    private const STATUS_SYNONIM = [
        'work' => 1,
        'check' => 2,
        'close' => 3
    ];

    public function writeComment(array $data)
    {
        return 1;
    }

    public function isWork()
    {
        if($this->status == self::STATUS_SYNONIM['work'])
            return 1;
        return 0;
    }

    public function close()
    {
        $this->status = self::STATUS_SYNONIM['close'];
        $this->save();
    }

    public function getStatus()
    {
        $res = array_keys(self::STATUS_SYNONIM, $this->status);
        return $res ? $res[0] : '';
    }

    public function indicator()
    {
        if($this->status == self::STATUS_SYNONIM['work'])
        {
            if(now() < $this->created_at->addMinutes($this->duration))
                return 'green';
            else
                return 'red';
        }
        else
            return 'grey';
    }

    public function worksheet()
    {
        return $this->hasOne(\App\Models\Worksheet::class, 'id', 'worksheet_id');
    }

    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }

    public function executors()
    {
        return $this->belongsToMany(\App\Models\User::class, 'sub_action_executors', 'sub_action_id');
    }

    public function reporters()
    {
        return $this->belongsToMany(\App\Models\User::class, 'sub_action_reports', 'sub_action_id');
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\SubActionComment::class, 'sub_action_id', 'id');
    }
}
