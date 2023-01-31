<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public function structures()
    {
        return $this->belongsToMany(\App\Models\Structure::class, 'company_structures', 'company_id')->withPivot('id');
    }

    public function brands()
    {
        return $this->belongsToMany(\App\Models\Brand::class, 'company_brands', 'company_id');
    }
}
