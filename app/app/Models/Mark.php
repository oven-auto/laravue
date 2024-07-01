<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\SortInterface;
use App\Models\Traits\Createable;

class Mark extends Model implements SortInterface
{
    use HasFactory, Createable;

    protected $guarded = [];



    /**
     * БРЕНД
     */
    public function brand()
    {
        return $this->hasOne(\App\Models\Brand::class, 'id', 'brand_id')->withDefault();
    }
}
