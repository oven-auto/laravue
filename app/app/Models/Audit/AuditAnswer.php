<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['question_id', 'author_id', 'type'];

    public const ANSWER_TYPES = ['positive', 'negative', 'neutral'];
}
