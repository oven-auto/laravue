<?php

namespace App\Repositories\Trafic;

use App\Models\Trafic;
use App\Http\Filters\TraficFilter;
use App\Services\Trafic\SaveTrafic;
use App\Services\Trafic\SaveTraficClient;
use App\Services\Trafic\SaveTraficControl;
use App\Services\Trafic\SaveTraficLink;
use App\Services\Trafic\SaveTraficMessage;
use App\Services\Trafic\SaveTraficProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;



class TraficRepository
{
    /**
     * CLOSE TRAFIC
     */
    public function close(Trafic $trafic)
    {
        if (isset($trafic->worksheet->id))
            throw new \Exception("Не могу закрыть трафик, из которого назначен рабочий лист");

        if($trafic->manager_id) 
        {
            $trafic->trafic_status_id = 4;
            $trafic->save(); 
        } 
    }



    /**
     * DELETE TRAFIC
     */
    public function delete(Trafic $trafic)
    {
        if (isset($trafic->worksheet->id))
            throw new \Exception("Не могу удалить трафик, из которого назначен рабочий лист");

        $trafic->delete();
    }



    /**
     * Сохранение трафика, работает как на создание , так и на изменение
     * @param Trafic $trafic Модель трафика, может быть пустой
     * @param array $data данные полученные с фронта, для в заполнения модели трафика
     * @return Trafic $trafic
     */
    public function save(Trafic $trafic, $data): Trafic
    {
        SaveTrafic::save($trafic, $data);

        SaveTraficProduct::save($trafic, $data);

        SaveTraficMessage::save($trafic, $data);
        
        SaveTraficClient::save($trafic, $data);

        SaveTraficControl::save($trafic, $data);

        return $trafic;
    }



    public function saveLink(Trafic $trafic, string $data)
    {
        SaveTraficLink::save($trafic, ['link' => $data]);
    }



    /**
     * КОНФИГУРИРОВАНИЕ ФИЛЬТРА ПО ПАРАМЕТРАМ
     * @param array $data данные для фильтра
     * @return Builder $query Builder
     */
    private function filter($data = []): Builder
    {
        $query = Trafic::select('trafics.*')->withTrashed();

        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        return $query
            ->filter($filter)
            ->orderBy(DB::raw('trafics.manager_id IS NULL'), 'DESC')
            ->orderBy('trafics.created_at', 'DESC')
            ->groupBy('trafics.id');
    }



    /**
     * КОЛ-ВО ТРАФИКОВ УДОВЛЕТВОРЯЮЩИХ УСЛОВИЯМ ФИЛЬТРАЦИИ
     * @param array $data данные для фильтра
     * @return int $result int
     */
    public function counter($data = []): int
    {
        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        $query = Trafic::select(DB::raw('count(trafics.id)'))
            ->withTrashed()
            ->filter($filter)
            ->groupBy('trafics.id')
            ->where('trafics.trafic_status_id', '<>', 6);
        
        $result = DB::table($query)->count();
        
        return $result;
    }



    /**
     * СПИСОК ТРАФИКОВ ВВИДЕ ПАГИНАЦИИ, ПОДХОДЯЩИХ УСЛОВИЯМ ФИЛЬТРАЦИИ
     * @param array $data данные для фильтра
     * @param integer $paginate не обязательное поле, по умолчанию 10
     * @return \Illuminate\Contracts\Pagination\Paginator $result
     */
    public function paginate($data = [], $paginate = 20): \Illuminate\Contracts\Pagination\Paginator
    {   
        $query = Trafic::select([
            'trafics.id',
            'trafics.created_at', 'trafics.updated_at',
            'trafics.author_id', 'trafics.trafic_zone_id',
            'trafics.trafic_chanel_id', 'trafics.company_id',
            'trafics.company_structure_id', 'trafics.trafic_appeal_id',
            'trafics.manager_id', 'trafics.trafic_status_id', 'trafics.processing_at'
        ]);

        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter)
            ->with([
                'needs', 'zone', 'chanel.myparent',
                'salon', 'structure', 'appeal', 'manager',
                'author', 'worksheet', 'processing', 'files', 
                'client.person', 'control', 'message', 'status'
            ])
            ->withTrashed()
            ->withCount(['links', 'files'])
            ->where('trafics.trafic_status_id', '<>', 6)
            ->orderBy(DB::raw('trafics.manager_id IS NULL'), 'DESC')
            ->orderBy('trafics.created_at', 'DESC')
            ->groupBy('trafics.id');
            
        $result = $query->simplePaginate($paginate);

        return $result;
    }



    /**
     * СПИСОК ТРАФИКОВ ВВИДЕ КОЛЛЕКЦИИ, ПОДХОДЯЩИХ ПОД УСЛОВИЕ ФИЛЬТРАЦИИ
     * @param array $data данные для фильтра
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($data = [], $limit = ''): \Illuminate\Database\Eloquent\Collection
    {
        $query = Trafic::select('trafics.*');

        $filter = app()->make(TraficFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter)
            ->with([
                'needs', 'zone', 'chanel.myparent',
                'salon', 'structure', 'appeal', 'manager',
                'author', 'worksheet', 'processing', 'files', 
                'client', 'control', 'message'
            ])
            ->withTrashed()
            ->withCount('links')
            ->where('trafics.trafic_status_id', '<>', 6)
            ->orderBy(DB::raw('trafics.manager_id IS NULL'), 'DESC')
            ->orderBy('trafics.created_at', 'DESC')
            ->groupBy('trafics.id');
        
        if($limit)
            $query->limit($limit);
        
        $result = $query->get();

        return $result;
    }



    /**
     * ЭКСПОРТ (нет постраничного вывода!!!), прошедших фильтрацию
     * @param array $data данные для фильтра
     * @return \Illuminate\Database\Eloquent\Collection $result
     */
    public function export($data = []): \Illuminate\Database\Eloquent\Collection
    {
        $limit = 1000;

        $result = $this->get($data, $limit);

        return $result;
    }



    /**
     * Метод поиск трафик по id, со всеми связными данными
     * @param int $id id-трафика
     * @return Trafic $result Trafic
     */
    public function find($id): Trafic
    {
        $result = Trafic::fullest()->find($id);

        return $result;
    }



    /**
     * ПОЛУЧИТЬ СПИСОК ТРАФИКОВ ДЛЯ ЖУРНАЛА ЗАДАЧ
     * @param $data данные для фильтрации
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTraficsForTaskList(array $data): \Illuminate\Database\Eloquent\Collection
    {
        $query = Trafic::select('trafics.*')->withTrashed();

        $filter = app()->make(\App\Http\Filters\TraficListFilter::class, ['queryParams' => array_filter($data)]);

        $query->filter($filter);

        $query->with([
            'needs','zone', 'chanel.myparent',
            'salon', 'structure', 'appeal', 'manager',
            'author', 'client', 'control', 'message'
        ]);

        $query->orderBy('trafic_controls.begin_at')->groupBy('trafics.id');

        $trafics = $query->get();

        return $trafics;
    }
}
