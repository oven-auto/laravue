<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Данная модель является отображением сущности представления, НЕ ТАБЛИЦЫ
 * СОХРАНЕНИЕ/УДАЛЕНИЕ в нее происходит под капотом sql
 */
class CarStatusType extends Model
{
    use HasFactory;

    public const VALUES = [
        'free'      => 'free',
        'reserved'   => 'reserved',
        'client'    => 'client',
        'saled'     => 'saled'
    ];



    public const STATES = [
        'free'      => 'Свободные',
        'reserved'   => 'Резервы',
        'client'    => 'Клиентские',
        'saled'     => 'Проданные'
    ];
}
