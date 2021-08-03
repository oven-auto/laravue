<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
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
}
