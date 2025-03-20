<?php

namespace App\Models\Audit;

use App\Models\Builders\AuditQuestionBuilder;
use App\Models\Scopes\Audit\Scopes\QuestionDefaultValueScope;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['text', 'audit_id', 'author_id', 'sort', 'name', 'weigth'];

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new QuestionDefaultValueScope());
    // }



    public function newEloquentBuilder($query)
    {
        return new AuditQuestionBuilder($query);
    }



    public function answers()
    {
        return $this->hasOne(\App\Models\Audit\AuditAnswer::class, 'question_id', 'id');
    }



    public function audit()
    {
        return $this->hasOne(\App\Models\Audit\Audit::class, 'id', 'audit_id');
    }



    public function getWeigth()
    {
        return $this->weigth ?? (100 - $this->physic_weigth) / ($this->count_out_weigth == 0 ? 1 : $this->count_out_weigth);
    }
}
