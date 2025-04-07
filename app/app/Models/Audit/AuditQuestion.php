<?php

namespace App\Models\Audit;

use App\Repositories\Audit\Interfaces\AuditSortInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditQuestion extends Model implements AuditSortInterface
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['text', 'audit_id', 'author_id', 'sort', 'name', 'weight'];

    protected $with = ['calcweight'];



    public function calcweight()
    {
        return $this->hasOne(\App\Models\Audit\AuditWeights::class, 'audit_id', 'audit_id');
    }



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function answers()
    {
        return $this->hasOne(\App\Models\Audit\AuditAnswer::class, 'question_id', 'id');
    }



    public function audit()
    {
        return $this->hasOne(\App\Models\Audit\Audit::class, 'id', 'audit_id');
    }



    public function getWeight()
    {
        return $this->weight ?? ($this->calcweight->weight ?? 0);
    }
}
