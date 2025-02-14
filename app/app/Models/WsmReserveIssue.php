<?php

namespace App\Models;

use App\Models\Interfaces\CommentInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WsmReserveIssue extends Model implements CommentInterface
{
    use HasFactory;

    protected $guarded;

    public $dates = ['date_at'];



    public function writeComment(array $data)
    {
        WsmReserveComment::create($data);
    }



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function decorator()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'decorator_id');
    }
}
