<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Filterable;

class Trafic extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'begin_at',
        'end_at',
        'processing_at',
        'deleted_at'
    ];

    public function close()
    {
        if($this->manager_id) {
            $this->trafic_status_id = 4;
            $this->save();
            $this->touch();
            return $this;
        }

        return 0;
    }

    public function process()
    {
        $this->trafic_status_id = 3;
        $this->save();
        $this->touch();
    }

    public function getPhoneMaskAttribute()
    {
        $phone = '';
        $from = $this->phone;
        if($this->phone)
            $phone = sprintf("+%s (%s) %s %s-%s",
                substr($from, 0, 1),
                substr($from, 1, 3),
                '***',
                substr($from, 7, 2),
                substr($from, 9)
            );
        return $phone;
    }

    public function status()
    {
        return $this->hasOne(\App\Models\TraficStatus::class, 'id', 'trafic_status_id')->withDefault();
    }

    public function scopeFullest($query)
    {
        return $query->with([
            'sex','zone','chanel',
            'salon','structure','appeal',
            'task','manager','author',
            'needs', 'worksheet.client','processing'
        ]);
    }

    public function saveNeeds()
    {
        return $this->hasMany(\App\Models\TraficNeed::class, 'trafic_id', 'id');
    }

    public function needs()
    {
        return $this->hasManyThrough(
            \App\Models\TraficProduct::class,
            \App\Models\TraficNeed::class,
            'trafic_id',
            'number',
            'id',
            'trafic_product_number'
        );
    }

    public function sex()
    {
        return $this->hasOne(\App\Models\TraficSex::class, 'id', 'trafic_sex_id')->withDefault();
    }

    public function zone()
    {
        return $this->hasOne(\App\Models\TraficZone::class, 'id', 'trafic_zone_id')->withDefault();
    }

    public function chanel()
    {
        return $this->hasOne(\App\Models\TraficChanel::class, 'id', 'trafic_chanel_id')->withDefault();
    }

    public function salon()
    {
        return $this->hasOne(\App\Models\Company::class, 'id', 'company_id')->withDefault();
    }

    public function structure()
    {
        return $this->hasOneThrough(
            \App\Models\Structure::class,
            \App\Models\CompanyStructure::class,
            'structure_id',
            'id',
            'company_structure_id',
            'id'
        );
    }

    public function appeal()
    {
        return $this->hasOneThrough(
            \App\Models\Appeal::class,
            \App\Models\TraficAppeal::class,
            'id',
            'id',
            'trafic_appeal_id',
            'appeal_id'
        );
    }

    public function task()
    {
        return $this->hasOne(\App\Models\Task::class, 'id','task_id')->withDefault();
    }

    public function manager()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'manager_id')->withDefault();
    }

    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id' , 'author_id')->withDefault();
    }

    public function worksheet()
    {
        return $this->hasOne(\App\Models\Worksheet::class, 'trafic_id','id')->withDefault();
    }

    public function processing()
    {
        return $this->hasOne(\App\Models\TraficProcessing::class, 'trafic_id', 'id')->withDefault();
    }
}
