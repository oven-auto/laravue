<?php

namespace App\Repositories\Trafic;

use App\Models\Trafic;
use Carbon\Carbon;
use DB;
use App\Http\Filters\TraficFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Репазиторий модели Trafic
 */
Class TraficRepository
{
    /**
     * Сохранение трафика, работает как на создание , так и на изменение
     * @param Trafic $trafic Модель трафика, может быть пустой
     * @param array $data данные полученные с фронта, для в заполнения модели трафика
     * @return Trafic $trafic
     */
    public function save(Trafic $trafic, $data) : Trafic
    {
        if(!$trafic->created_at)
            $trafic->created_at     = isset($data['time']) ? date('Y-m-d H:i',\strtotime($data['time'])) : date('Y-m-d H:i:s');

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
     * Метод задает запрос на получение списка трафиков удовлетворяющих заданным свойствам фильтра
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
     * Метод возращает количество трафиков, удовлетворяющих условию фильтра
     * @param array $data данные для фильтра
     * @return int $result int
     */
    public function counter($data = []) : int
    {
        $query = Trafic::select(\DB::raw('count(trafics.id)'))->withTrashed();
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);
        $query->filter($filter)
            ->groupBy('trafics.id');
        $query->where('trafics.trafic_status_id', '<>', 6);
        $result = $query->get()->count();
        return $result;
    }

    /**
     * Метод возращает постраничную коллекию трафиков, прошедших фильтрацию
     * @param array $data данные для фильтра
     * @param integer $paginate не обязательное поле, по умолчанию 10
     * @return \Illuminate\Contracts\Pagination\Paginator $result \Illuminate\Contracts\Pagination\Paginator
     */
    public function paginate($data = [], $paginate = 10) : \Illuminate\Contracts\Pagination\Paginator
    {
        //$query = Trafic::select('trafics.*')->withTrashed();
        $query = $this->filter($data);
        $query->with([
            'needs', 'sex', 'zone', 'chanel.myparent',
            'salon', 'structure', 'appeal', 'manager',
            'author', 'worksheet', 'processing', 'files', 'person',
        ])->withCount('links');

        $query->where('trafics.trafic_status_id', '<>', 6);

        $result = $query->simplePaginate($paginate);

        return $result;
    }

    public function get($data = [])
    {
        //$query = Trafic::select('trafics.*')->withTrashed();
        $query = $this->filter($data);
        $query->with([
            'needs', 'sex', 'zone', 'chanel.myparent',
            'salon', 'structure', 'appeal', 'manager',
            'author', /*'worksheet', 'processing', 'files',*/ 'person'
        ]);
        $result = $query->orderBy('trafics.begin_at')->get();
        return $result;
    }

    /**
     * Метод возращает коллекию трафиков (нет постраничного вывода!!!), прошедших фильтрацию
     * @param array $data данные для фильтра
     * @return \Illuminate\Database\Eloquent\Collection $result \Illuminate\Database\Eloquent\Collection
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
    public function find($id)
    {
        $result = Trafic::fullest()->find($id);
        return $result;
    }

    public function getTraficsForTaskList(array $data)
    {
        $query = Trafic::select('trafics.*')->withTrashed();

        $filter = app()->make(\App\Http\Filters\TraficListFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

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
