<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Worksheet\Service\WSMService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WSMCredit extends Model
{
    use HasFactory, Filterable;

    protected $guarded = [];

    public $table = 'wsm_credits';



    public function scopeAllRelations($query)
    {
        $query->with([
            'state',
            'worksheet',
            'debtor',
            'tactic',
            'creditor',
            'status',
            'author',
            'award',
            'contract',
            'calculation',
            'deduction',
            'services' => function($serQ){
                $serQ->with([
                    'author',
                    'provider',
                    'payment',
                    'award',
                    'contract' => function($q){
                        $q->with(['decorator', 'manager']);
                    },
                    'deduction', 
                    'car.carable' => function($q){
                        $q->with(['brand','mark']);
                    },
                ]);
            },
            'approximates',
            'car.carable' => function($q){
                $q->with(['brand','mark']);
            },
        ]);
    }



    public function state()
    {
        return $this->hasOne(\App\Models\WSMCreditState::class, 'wsm_credit_id', 'id')->withDefault();
    }



    public function worksheet()
    {
        return $this->hasOne(\App\Models\Worksheet::class, 'id', 'worksheet_id');
    }



    public function debtor()
    {
        return $this->hasOne(\App\Models\Client::class, 'id', 'debtor_id');
    }



    public function tactic()
    {
        return $this->hasOne(\App\Models\CreditTactic::class, 'id', 'calculation_type');
    }



    public function creditor()
    {
        return $this->hasOne(\App\Models\Client::class, 'id', 'creditor_id');
    }



    public function status()
    {
        return $this->hasOne(\App\Models\CreditStatus::class, 'id', 'status_id');
    }



    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    public function award()
    {
        return $this->hasOne(\App\Models\WSMCreditAward::class, 'wsm_credit_id', 'id');
    }



    public function contract()
    {
        return $this->hasOne(\App\Models\WSMCreditContract::class, 'wsm_credit_id', 'id');
    }



    public function calculation()
    {
        return $this->hasOne(\App\Models\WSMCreditCalculation::class, 'wsm_credit_id', 'id');
    }



    public function deduction()
    {
        return $this->hasOne(\App\Models\WSMCreditDeduction::class, 'wsm_credit_id', 'id');
    }
    


    public function services()
    {
        return $this->belongsToMany(WSMService::class, 'wsm_credit_services', 'wsm_credit_id', 'wsm_service_id', 'id');
    }



    public function approximates()
    {
        return $this->belongsToMany(Service::class, 'wsm_credit_approximate_services', 'wsm_credit_id', 'service_id', 'id');
    }



    public function car()
    {
        return $this->hasOne(WSMCreditCar::class, 'wsm_credit_id', 'id');
    }
}
