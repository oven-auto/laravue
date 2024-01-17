<?php

namespace App\Repositories\Trafic;

use App\Models\Trafic;
use Carbon\Carbon;
use DB;
use App\Http\Filters\TraficFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Репазиторий модели Trafic
 * - ПОДСТАВИТЬ ПРИ СОЗДАНИИ ТРАФИКА СВОЮ ДАТУ СОЗДАНИЯ В created_at
 * - Сохранение трафика
 * - КОНФИГУРИРОВАНИЕ ФИЛЬТРА ПО ПАРАМЕТРАМ
 * - КОЛ-ВО ТРАФИКОВ УДОВЛЕТВОРЯЮЩИХ УСЛОВИЯМ ФИЛЬТРАЦИИ
 * - СПИСОК ТРАФИКОВ ВВИДЕ ПАГИНАЦИИ, ПОДХОДЯЩИХ УСЛОВИЯМ ФИЛЬТРАЦИИ
 * - СПИСОК ТРАФИКОВ ВВИДЕ КОЛЛЕКЦИИ, ПОДХОДЯЩИХ ПОД УСЛОВИЕ ФИЛЬТРАЦИИ
 * - ЭКСПОРТ
 * - Метод поиск трафик по id
 * - ПОЛУЧИТЬ СПИСОК ТРАФИКОВ ДЛЯ ЖУРНАЛА ЗАДАЧ
 *
 * 05-02-2023
 */
Class TraficRepository
{
    /**
     * ПОДСТАВИТЬ ПРИ СОЗДАНИИ ТРАФИКА СВОЮ ДАТУ СОЗДАНИЯ В created_at
     * @param string
     * @return string
     */
    private function createDate($string) : string
    {
        if(date('Y',\strtotime($string)) != now()->year)
            return now();
        else
            return date('Y-m-d H:i',\strtotime($string));

        return date('Y-m-d H:i:s');
    }



    /**
     * Сохранение трафика, работает как на создание , так и на изменение
     * @param Trafic $trafic Модель трафика, может быть пустой
     * @param array $data данные полученные с фронта, для в заполнения модели трафика
     * @return Trafic $trafic
     */
    public function save(Trafic $trafic, $data) : Trafic
    {
        if(!$trafic->created_at)
            $trafic->created_at = isset($data['time']) ?? $this->createDate($data['time']);

        $trafic->fill([
            'author_id'             => $data['author_id'] ?? $trafic->author_id,
            'firstname'             => $data['firstname'] ?? $trafic->firstname,
            'lastname'              => $data['lastname'] ?? $trafic->lastname,
            'fathername'            => $data['fathername'] ?? $trafic->fathername,
            'phone'                 => isset($data['phone']) ? preg_replace("/[^,.0-9]/", '', $data['phone']) : $trafic->phone,
            'email'                 => $data['email'] ?? $trafic->email,
            'comment'               => $data['comment'] ?? $trafic->comment,
            'trafic_sex_id'         => $data['trafic_sex_id'] ?? $trafic->trafic_sex_id,
            'trafic_zone_id'        => $data['trafic_zone_id'] ?? $trafic->trafic_zone_id,
            'trafic_chanel_id'      => $data['trafic_chanel_id'] ?? $trafic->trafic_chanel_id,
            'company_id'            => $data['trafic_brand_id'] ?? $trafic->company_id,
            'company_structure_id'  => $data['trafic_section_id'] ?? $trafic->company_structure_id,
            'trafic_appeal_id'      => $data['trafic_appeal_id'] ?? $trafic->trafic_appeal_id,
            'begin_at'              => isset($data['begin_at']) ? date('Y-m-d H:i',\strtotime($data['begin_at'])) : $trafic->begin_at,
            'end_at'                => isset($data['end_at']) ? date('Y-m-d H:i',\strtotime($data['end_at'])) : $trafic->end_at,
            'manager_id'            => $data['manager_id'] ?? $trafic->manager_id,
            'interval'              => $data['trafic_interval'] ?? $trafic->interval,
            'client_type_id'        => isset($data['person_type_id'])
                                        ? $data['person_type_id']
                                        : (isset($data['client_type_id'])
                                            ? $data['client_type_id']
                                            : $trafic->client_type_id),
            'inn'                   => isset($data['inn']) ? $data['inn'] : NULL,
            'company_name'          => isset($data['company_name']) ? $data['company_name'] : NULL,
        ]);

        $trafic->save();

        if(isset($data['trafic_need_id'])) {
            $trafic->saveNeeds()->delete();
            foreach($data['trafic_need_id'] as $item)
                $trafic->saveNeeds()->create(['trafic_product_number' => $item['id']]);
        }
        return $trafic;
    }



    /**
     * КОНФИГУРИРОВАНИЕ ФИЛЬТРА ПО ПАРАМЕТРАМ
     * @param array $data данные для фильтра
     * @return Builder $query Builder
     */
    private function filter($data = []) : Builder
    {
        $query = Trafic::select('trafics.*')->withTrashed();

        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        return $query
            ->filter($filter)
            ->orderBy(DB::raw('trafics.manager_id IS NULL'),'DESC')
            ->orderBy('trafics.created_at','DESC')
            ->groupBy('trafics.id');
    }



    /**
     * КОЛ-ВО ТРАФИКОВ УДОВЛЕТВОРЯЮЩИХ УСЛОВИЯМ ФИЛЬТРАЦИИ
     * @param array $data данные для фильтра
     * @return int $result int
     */
    public function counter($data = []) : int
    {
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select(\DB::raw('count(trafics.id)'))
            ->withTrashed()
            ->filter($filter)
            ->groupBy('trafics.id')
            ->where('trafics.trafic_status_id', '<>', 6);

        $result = $query->get()->count();

        return $result;
    }



    /**
     * СПИСОК ТРАФИКОВ ВВИДЕ ПАГИНАЦИИ, ПОДХОДЯЩИХ УСЛОВИЯМ ФИЛЬТРАЦИИ
     * @param array $data данные для фильтра
     * @param integer $paginate не обязательное поле, по умолчанию 10
     * @return \Illuminate\Contracts\Pagination\Paginator $result
     */
    public function paginate($data = [], $paginate = 10) : \Illuminate\Contracts\Pagination\Paginator
    {
        $query = $this->filter($data)
            ->with([
                'needs', 'sex', 'zone', 'chanel.myparent',
                'salon', 'structure', 'appeal', 'manager',
                'author', 'worksheet', 'processing', 'files', 'person',
            ])
            ->withCount('links')
            ->where('trafics.trafic_status_id', '<>', 6);

        $result = $query->simplePaginate($paginate);

        return $result;
    }



    /**
     * СПИСОК ТРАФИКОВ ВВИДЕ КОЛЛЕКЦИИ, ПОДХОДЯЩИХ ПОД УСЛОВИЕ ФИЛЬТРАЦИИ
     * @param array $data данные для фильтра
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($data = []) : \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->filter($data)
            ->with([
                'needs', 'sex', 'zone', 'chanel.myparent',
                'salon', 'structure', 'appeal', 'manager',
                'author', /*'worksheet', 'processing', 'files',*/ 'person'
            ])
            ->orderBy('trafics.begin_at');

        $result = $query->get();

        return $result;
    }



    /**
     * ЭКСПОРТ (нет постраничного вывода!!!), прошедших фильтрацию
     * @param array $data данные для фильтра
     * @return \Illuminate\Database\Eloquent\Collection $result
     */
    public function export($data = []) : \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->filter($data);

        $result = $query->get();

        return $result;
    }



    /**
     * Метод поиск трафик по id, со всеми связными данными
     * @param int $id id-трафика
     * @return Trafic $result Trafic
     */
    public function find($id) : Trafic
    {
        $result = Trafic::fullest()->find($id);

        return $result;
    }



    /**
     * ПОЛУЧИТЬ СПИСОК ТРАФИКОВ ДЛЯ ЖУРНАЛА ЗАДАЧ
     * @param $data данные для фильтрации
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTraficsForTaskList(array $data) : \Illuminate\Database\Eloquent\Collection
    {
        $query = Trafic::select('trafics.*')->withTrashed();

        $filter = app()->make(\App\Http\Filters\TraficListFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

        //Получить ожидающие которые может обработать пользователь
        $query->leftJoin('trafic_appeals', 'trafic_appeals.id', 'trafics.trafic_appeal_id');//добавляем таблицу ссылок цели обращения
        $query->orWhere(function($q){
            $q->where('trafics.trafic_status_id',1);//только ожидающие
            $q->whereIn('trafic_appeals.appeal_id', auth()->user()->appeals->pluck('id'));//цель должна быть у пользователя
            $q->whereIn('trafics.company_structure_id', auth()->user()->structures->pluck('company_structure_id'));//структура трафика равна структуре цели обращения
        });

        $query->with([
            'needs', 'sex', 'zone', 'chanel.myparent',
            'salon', 'structure', 'appeal', 'manager',
            'author', 'person'
        ]);

        $query->orderBy('trafics.begin_at')->groupBy('trafics.id');

        $trafics = $query->get();

        return $trafics;
    }
}
