<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['text', 'audit_id', 'author_id', 'sort', 'name', 'weigth'];



    public function answers()
    {
        return $this->hasOne(\App\Models\Audit\AuditAnswer::class, 'question_id', 'id');
    }
}
