<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'author_id',
        'name'
    ];



    /**
     * КАТЕГОРИЯ ПРОДУКТА
     */
    public function category()
    {
        return $this->hasOne(\App\Models\ServiceCategory::class, 'id', 'category_id');
    }



    /**
     * ПРИМЕНЯЕМОСТЬ
     */
    public function applicabilities()
    {
        return $this->hasMany(\App\Models\ServiceApplicability::class, 'service_id', 'id');
    }



    public function calculation()
    {
        return $this->hasOne(\App\Models\ServiceCalculation::class, 'service_id', 'id');
    }



    public function prolongation()
    {
        return $this->hasOne(\App\Models\ServiceProlongation::class, 'service_id', 'id');
    }



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function providers()
    {
        return $this->belongsToMany(\App\Models\Client::class, 'service_providers', 'service_id', 'provider_id', 'id');
    }
}
