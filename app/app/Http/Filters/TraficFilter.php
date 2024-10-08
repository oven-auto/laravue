<?php

namespace App\Http\Filters;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Collection;

class TraficFilter extends AbstractFilter
{
    public const MANAGER_ID = 'manager_id';
    public const INTERVAL = 'interval';
    public const IDS = 'ids';
    public const SEX_ID = 'sex_id';
    public const ZONE_ID = 'zones_id';
    public const CHANEL_ID = 'chanel_id';
    public const COMPANY_IDS = 'brand_ids';
    public const STRUCTURE_IDS = 'section_ids';
    public const APPEAL_IDS = 'appeal_ids';
    public const TASK_ID = 'action_id';
    public const INPUT = 'input';
    public const AUTHOR_ID = 'author_id';
    public const CREATE_START = 'register_begin';
    public const CREATE_END = 'register_end';
    public const PROCESSING_START = 'processing_begin';
    public const PROCESSING_END = 'processing_end';
    public const STATUS_ID = 'status_id';
    public const NEED_IDS = 'need_ids';
    public const SUPER_DIAMOND_SEARCH = 'super_diamond_search';
    public const AUDIT_AUTHOR_ID = 'audit_author_id';
    public const AUDIT_SCENARIO_ID = 'scenario';
    public const AUDIT_STATUS_ID = 'status_audit_id';
    public const MODEL_IDS = 'model_ids';
    public const PERSON_TYPE_ID = 'person_type_id';
    public const IS_PRODUCT = 'is_product';
    public const BRANDS = 'brands';
    public const CONTROL_DATE = 'control_date';
    public const SHOW = 'show';
    public const DATE_FOR_CLOSING = 'date_for_closing';
    public const SHOW_MONTH = 'show_month';
    public const WORKING = 'working';
    public $countElements = 0;

    protected function getCallbacks(): array
    {
        return [
            self::MANAGER_ID          => [$this, 'managerId'], //
            self::INTERVAL            => [$this, 'interval'], //string
            self::IDS                 => [$this, 'ids'], //string
            self::SEX_ID              => [$this, 'sexId'], //val
            self::ZONE_ID             => [$this, 'zoneId'], //array
            self::CHANEL_ID           => [$this, 'chanelId'], //array
            self::COMPANY_IDS         => [$this, 'companyIds'], //val
            self::STRUCTURE_IDS       => [$this, 'companyStructureIds'], //val
            self::APPEAL_IDS          => [$this, 'appealIds'], //val
            self::TASK_ID             => [$this, 'taskId'], //val
            self::INPUT               => [$this, 'input'], //val
            self::AUTHOR_ID           => [$this, 'authorId'],
            self::CREATE_START        => [$this, 'createStart'],
            self::CREATE_END          => [$this, 'createEnd'],
            self::PROCESSING_START    => [$this, 'processingStart'],
            self::PROCESSING_END      => [$this, 'processingEnd'],
            self::STATUS_ID           => [$this, 'statusId'],
            self::NEED_IDS            => [$this, 'needIds'],
            self::SUPER_DIAMOND_SEARCH => [$this, 'superDiamondSearch'],
            self::AUDIT_AUTHOR_ID     => [$this, 'auditAuthorId'],
            self::AUDIT_SCENARIO_ID   => [$this, 'auditScenarioId'],
            self::AUDIT_STATUS_ID     => [$this, 'auditStatusId'],
            self::MODEL_IDS           => [$this, 'modelIds'],
            self::PERSON_TYPE_ID      => [$this, 'personTypeId'],
            self::IS_PRODUCT          => [$this, 'isProduct'],
            self::BRANDS              => [$this, 'brands'],
            self::CONTROL_DATE        => [$this, 'controlDate'],
            self::SHOW                => [$this, 'show'],
            self::DATE_FOR_CLOSING    => [$this, 'dateForClosing'],
            self::SHOW_MONTH          => [$this, 'showMonth'],
            self::WORKING             => [$this, 'working'],
        ];
    }

    public function working(Builder $builder, $value)
    {
        $builder->whereIn('trafics.trafic_status_id', $value);
    }

    public function showMonth(Builder $builder, $value)
    {
        $date = $this->formatDate($value);
        $carbon =  Carbon::createFromFormat('Y-m-d', $date);

        $builder->where(function ($query) use ($carbon) {
            $query
                ->whereYear('trafics.created_at', '=', $carbon->year)
                ->whereMonth('trafics.created_at', '=', $carbon->month);
        });
    }

    public function show(Builder $builder, $value)
    {
        if ($value == 'closing')
            $builder->whereNotIn('trafics.trafic_status_id', [1, 2, 6]);
        else if ($value == 'opening')
            $builder->whereIn('trafics.trafic_status_id', [1, 2, 6]);
    }

    public function dateForClosing(Builder $builder, $value)
    {
        $date = $this->formatDate($value);

        $builder->whereDate('trafics.begin_at', '=', $date);
    }

    public function controlDate(Builder $builder, $value)
    {
        $date = $this->formatDate($value);

        if (now() >= $date)
            //Условие что за текющую дату либо ранее, если сегодня меньше либо равно указанной даты
            $builder->whereDate('trafics.begin_at', '<=', $date);
        else
            //Условие только за указанную дату
            $builder->whereDate('trafics.begin_at', '=', $date);
    }

    public function brands(Builder $builder, $value)
    {
        $builder->whereIn('company_id', $value);
    }

    // private function checkJoin(Builder $builder, $table)
    // {
    //     $res = collect($builder->getQuery()->joins)->pluck('table')->contains($table);
    //     return $res;
    // }

    public function modelIds(Builder $builder, $value)
    {
        if (!$this->checkJoin($builder, 'trafic_needs'))
            $builder->leftJoin('trafic_needs', 'trafic_needs.trafic_id', '=', 'trafics.id');
        $builder->whereIn('trafic_needs.trafic_product_number', $value);
    }

    public function isProduct(Builder $builder, $value)
    {
        if (!$this->checkJoin($builder, 'trafic_needs'))
            $builder->leftJoin('trafic_needs', 'trafic_needs.trafic_id', '=', 'trafics.id');
        if ($value == 2)
            $builder->havingRaw('count(trafic_needs.trafic_id) > 0');
        elseif ($value == 1)
            $builder->havingRaw('count(trafic_needs.trafic_id) = 0');
    }

    public function personTypeId(Builder $builder, $value)
    {
        if (is_array($value))
            $builder->whereIn('trafics.client_type_id', $value);
        else
            $builder->where('trafics.client_type_id', $value);
    }

    public function auditAuthorId(Builder $builder, $value)
    {
        if (!$this->checkJoin($builder, 'trafic_processings'))
            $builder->leftJoin('trafic_processings', 'trafic_processings.trafic_id', 'trafics.id');
        $builder->where('trafic_processings.user_id', $value);
    }

    public function auditScenarioId(Builder $builder, $value)
    {
        if (!$this->checkJoin($builder, 'trafic_processings'))
            $builder->leftJoin('trafic_processings', 'trafic_processings.trafic_id', 'trafics.id');

        $builder->where('trafic_processings.audit_standart_id', $value);
    }

    public function auditStatusId(Builder $builder, $value)
    {
        if (!$this->checkJoin($builder, 'trafic_processings'))
            $builder->leftJoin('trafic_processings', 'trafic_processings.trafic_id', 'trafics.id');
        if ($value == 1)
            $builder->where('trafic_processings.status', 1);
        if ($value == 2)
            $builder->where('trafic_processings.status', 0);
        if ($value == 3)
            $builder->where('trafic_processings.status', NULL);
    }

    private function setCountElements($data)
    {
        if (\is_array($data) || \is_object($data))
            $this->countElements += count($data);
        if (\is_string($data) || \is_integer($data) || \is_numeric($data))
            $this->countElements += ($data);
    }

    public function getCountElements()
    {
        return $this->countElements;
    }

    public function superDiamondSearch(Builder $builder, $value)
    {
        if ($value)
            $builder->havingRaw('count(*) >= ?', [$this->getCountElements()]);
        else
            $builder->havingRaw('count(*) >= ?', [0]);
    }

    //Заданные продукты или услуги [trafic_product_number, .... , trafic_product_number]
    public function needIds(Builder $builder, $value)
    {
        $this->setCountElements($value);
        $builder->leftJoin('trafic_needs', 'trafic_needs.trafic_id', '=', 'trafics.id')
            ->addSelect(DB::raw('max(trafic_needs.trafic_product_number)'))
            ->whereIn('trafic_needs.trafic_product_number', $value);
    }

    //Заданные статусы трафика [trafic_status_id, ... , trafic_status_id]
    public function statusId(Builder $builder, $value)
    {
        if (is_array($value))
            $builder->whereIn('trafics.trafic_status_id', $value);
        if (is_string($value))
            $builder->where('trafics.trafic_status_id', $value);
    }

    //Начало обработки [dd.mm.yyyy]
    public function processingStart(Builder $builder, $value)
    {
        $builder->whereDate('trafics.processing_at', '>=', $this->formatDate($value));
    }

    //Конец обработки [dd.mm.yyyy]
    public function processingEnd(Builder $builder, $value)
    {
        $builder->whereDate('trafics.processing_at', '<=', $this->formatDate($value));
    }

    //Начало создания [dd.mm.yyyy]
    public function createStart(Builder $builder, $value)
    {
        $builder->whereDate('trafics.created_at', '>=', $this->formatDate($value));
    }

    //Конец создания [dd.mm.yyyy]
    public function createEnd(Builder $builder, $value)
    {
        $builder->whereDate('trafics.created_at', '<=', $this->formatDate($value));
    }

    //Авторы [user_id, ... , user_id]
    public function authorId(Builder $builder, $value)
    {
        $builder->whereIn('trafics.author_id', $value);
    }

    //Менеджеры [user_id, ... , user_id]
    public function managerId(Builder $builder, $value)
    {
        if (is_array($value))
            $builder->where(function ($query) use ($value) {
                $query
                    ->whereIn('trafics.manager_id', $value);
                //->orWhere('trafics.manager_id', NULL);
            });
        else
            $builder->where('trafics.manager_id', $value);
    }

    //Интервал {yesterday, today, week, month}
    public function interval(Builder $builder, $value)
    {
        switch ($value) {
            case 'month':
                $builder->where(function ($query) {
                    $query
                        ->whereYear('trafics.created_at', '=', now()->year)
                        ->whereMonth('trafics.created_at', '=', now()->month);
                });
                break;
            case 'week':
                $builder->whereBetween('trafics.created_at', [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;
            case 'today':
                $builder->whereDate('trafics.created_at', now());
                break;
            case 'yesterday':
                $builder->whereDate('trafics.created_at', now()->subDay());
                break;
            default:
                break;
        }
    }

    //Заданные айдишники трафиков [id, ... , id] / id
    public function ids(Builder $builder, $value)
    {
        if (\is_array($value))
            $builder->whereIn('trafics.id', $value);
        if (\is_string($value))
            $builder->whereIn('trafics.id', explode(',', $value));
    }

    //Заданный пол клиента трафика id
    public function sexId(Builder $builder, $value)
    {
        $builder->where('trafics.trafic_sex_id', $value);
    }

    //Заданные зоны трафика [zone_id, ... , zone_id]
    public function zoneId(Builder $builder, $value)
    {
        $builder->whereIn('trafics.trafic_zone_id', $value);
    }

    //Заданные каналы трафика [chanel_id, ... , chanel_id]
    public function chanelId(Builder $builder, $value)
    {
        $chanels = \App\Models\TraficChanel::select('trafic_chanels.id')
            ->whereIn('trafic_chanels.id', $value)
            ->orWhereIn('trafic_chanels.parent', $value)
            ->pluck('id');
        $builder->whereIn('trafics.trafic_chanel_id', $chanels);
    }

    //Заданная компания company_id
    public function companyIds(Builder $builder, $value)
    {
        if (is_array($value))
            $builder->whereIn('trafics.company_id', $value);
        elseif (is_numeric($value) || is_string($value))
            $builder->where('trafics.company_id', $value);
    }

    //Заданная структура компания structure_id
    public function companyStructureIds(Builder $builder, $value)
    {
        $builder->leftJoin('company_structures', 'company_structures.id', 'trafics.company_structure_id');
        $builder->whereIn('company_structures.structure_id', $value);
    }

    //Заданная цель обращения appeal_id
    public function appealIds(Builder $builder, $value)
    {
        if (is_array($value)) {
            $builder->leftJoin('trafic_appeals', 'trafic_appeals.id', 'trafics.trafic_appeal_id');
            $builder->whereIn('trafic_appeals.appeal_id', $value);
        }
    }

    //Заданная назначенное действиие task_id
    public function taskId(Builder $builder, $value)
    {
        $builder->where('trafics.task_id', $value);
    }

    //Поиск в текстовом поле по совпадению телефона/имени/Фамилии/айди трафика
    public function input(Builder $builder, $value)
    {
        $isCommand = strpos($value, '/');

        if ($isCommand === 0) {
            $builder->where('trafics.id', 'LIKE', '%' . trim($value, '/') . '%');
            return;
        }

        if (strpos($value, '+') === 0) {
            $value = trim($value, '+');
            $phone = "$value%";
            $builder->where('trafics.phone',      'LIKE', "$phone");
        } else
            $builder->where(function ($query) use ($value) {
                $query
                    ->where('trafics.phone',      'LIKE', "%$value%")
                    ->orWhere('trafics.firstname',  'LIKE', "%$value%")
                    ->orWhere('trafics.lastname',   'LIKE', "%$value%")
                    ->orWhere('trafics.id',         'LIKE', "%$value%");
            });
    }
}
