<?php

namespace App\Models;

use App\Models\Interfaces\CommentInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCar extends Model implements CommentInterface
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function writeComment(array $data)
    {
        ClientComment::create($data);
    }

    public function brand()
    {
        return $this->hasOne(\App\Models\Brand::class, 'id', 'brand_id')->withDefault();
    }

    public function mark()
    {
        return $this->hasOne(\App\Models\Mark::class, 'id', 'mark_id')->withDefault();
    }

    public function bodywork()
    {
        return $this->hasOne(\App\Models\BodyWork::class, 'id', 'body_work_id')->withDefault();
    }

    public function client()
    {
        return $this->hasOne(\App\Models\Client::class, 'id', 'client_id')->withDefault();
    }

    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id')->withDefault();
    }

    public function editor()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'editor_id')->withDefault();
    }
}
