<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'author_id', 'appeal_id', 'bonus', 'malus', 'complete'];



    public function questions()
    {
        return $this->hasOne(\App\Models\Audit\AuditQuestion::class, 'audit_id', 'id');
    }



    public function appeal()
    {
        return $this->hasOne(\App\Models\Appeal::class, 'id', 'appeal_id');
    }
}
