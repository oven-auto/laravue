<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\SortInterface;

class Mark extends Model implements SortInterface
{
    use HasFactory;

    protected $guarded = [];

    public function info()
    {
        return $this->hasOne(\App\Models\MarkInfo::class, 'mark_id', 'id')->withDefault();
    }

    public function properties()
    {
        return $this->belongsToMany(\App\Models\Property::class, 'mark_properties', 'mark_id')->withPivot('value');
    }

    public function icon()
    {
        return $this->hasOne(\App\Models\MarkIcon::class, 'mark_id', 'id')->withDefault();
    }

    public function banner()
    {
        return $this->hasOne(\App\Models\MarkBanner::class, 'mark_id', 'id')->withDefault();
    }

    public function document()
    {
        return $this->hasOne(\App\Models\MarkDocument::class, 'mark_id', 'id')->withDefault();
    }

    public function bodywork()
    {
        return $this->hasOne(\App\Models\BodyWork::class, 'id', 'body_work_id')->withDefault();
    }

    public function brand()
    {
        return $this->hasOne(\App\Models\Brand::class, 'id', 'brand_id')->withDefault();
    }

    public function markcolors()
    {
        return $this->hasMany(\App\Models\MarkColor::class, 'mark_id', 'id')->with('color');
    }

    public function basecomplectation()
    {
        return $this->hasOne(\App\Models\Complectation::class, 'mark_id', 'id')->where('status',1)->orderBy('price')->withDefault();
    }
}
