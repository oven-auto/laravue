<?php

namespace App\Models\Audit;

use App\Repositories\Audit\Interfaces\AuditSortInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditSubAnswer extends Model implements AuditSortInterface
{
    use HasFactory;

    protected $guarded = [];

    
}
