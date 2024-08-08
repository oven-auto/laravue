<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complectation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /* СВЯЗИ */

    /**
     * АВТОР
     */
    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id');
    }



    /**
     * МОДЕЛЬ И ИЗ НЕЕ ЖЕ БРЕНД
     */
    public function mark()
    {
        return $this->hasOne(\App\Models\Mark::class, 'id', 'mark_id')->with('brand');
    }



    /**
     * ТИП ТС
     */
    public function vehicle()
    {
        return $this->hasOne(\App\Models\VehicleType::class, 'id', 'vehicle_type_id');
    }



    /**
     * ТИП КУЗОВА
     */
    public function bodywork()
    {
        return $this->hasOne(\App\Models\BodyWork::class, 'id', 'body_work_id');
    }



    /**
     * МОТОР
     */
    public function motor()
    {
        return $this->hasOne(\App\Models\Motor::class, 'id', 'motor_id')
            ->with(['transmission', 'driver', 'type']);
    }



    /**
     * СТРАНА ПРОИЗВОДСТВА
     */
    public function factory()
    {
        return $this->hasOne(\App\Models\Factory::class, 'id', 'factory_id');
    }



    /**
     * ФАИЛЫ
     */
    public function file()
    {
        return $this->hasOne(\App\Models\ComplectationFile::class, 'complectation_id', 'id');
    }



    /**
     * ИСТОРИЯ
     */
    public function history()
    {
        return $this->hasMany(\App\Models\ComplectationHistory::class, 'complectation_id', 'id');
    }



    /**
     * LAST HISTORY
     */
    public function last_history()
    {
        return $this->hasOne(\App\Models\ComplectationHistory::class, 'complectation_id', 'id')->orderBy('id', 'DESC');
    }



    /**
     * GET FILE URL
     */
    public function getUrlFile()
    {
        if ($this->file)
            return \WebUrl::make_link($this->file->file, false);
        return '';
    }



    public function current_price()
    {
        return $this->hasOne(\App\Models\ComplectationCurrentPrice::class, 'complectation_id', 'id')->withDefault();
    }



    public function prices()
    {
        return $this->hasMany(\App\Models\ComplectationPrice::class, 'complectation_id', 'id');
    }



    public function alias()
    {
        return $this->hasOne(\App\Models\ComplectationMarkAlias::class, 'complectation_id', 'id');
    }



    /**
     * ACCESSORS
     */

    public function getPriceAttribute()
    {
        return $this->current_price->price ?? 0;
    }



    public function getPriceDateAttribute()
    {
        return $this->current_price->begin_at ? $this->current_price->begin_at->format('d.m.Y') : '';
    }



    public function saveAlias($aliasId)
    {
        $this->alias()->updateOrCreate([
            'complectation_id' => $this->id,
            'mark_alias_id' => $aliasId
        ]);
    }
}
