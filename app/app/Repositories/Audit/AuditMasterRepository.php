<?php

namespace App\Repositories\Audit;

use App\Http\Filters\AuditMasterFilter;
use App\Models\Audit\AuditMaster;
use App\Models\Audit\AuditQuestion;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

Class AuditMasterRepository
{
    public function arbitr(int $id)
    {
        $audit = $this->getById($id);

        $audit->arbitrSatus();
    }



    public function delete(int $id)
    {
        $audit = $this->getById($id);        

        $audit->delete();

        return 1;
    }



    public function restore(int $id)
    {
        $audit = $this->getById($id);

        $audit->restore();

        return 1;
    }



    public function getById(int $id) : AuditMaster
    {   
        return AuditMaster::withTrashed()->findOrFail($id);
    }



    public function isExist(array $data) : bool
    {
        $existed = $this->getAll(Arr::only($data, ['trafic_id', 'audit_id']));
        
        if($existed->count())
            return 1;
        return 0;
    }



    public function getAll(array $data)
    {
        $query = AuditMaster::query();

        if(isset($data['trafic_id']))
            $query->where('trafic_id', $data['trafic_id']);

        if(isset($data['audit_id']))
            $query->where('audit_id',  $data['audit_id']);

        $result = $query->get();

        return $result;
    }



    public function checkCompleted(AuditMaster $master)
    {
        $arr['result'] = json_decode($master->result,1);

        $currentPoint = $this->calcPoint($arr);
        
        $complete = $master->audit->complete;
        
        $master->completed = $complete < $currentPoint ? true : false;
      
        $master->save();
    }



    public function calcPoint(array $data) : int|float
    {
        $total = 100; //общее кол-во балов
        $positive = 0; //сумма балов вопросов ответ на который "ДА"
        $negative = 0; // Сумма баллов вопросов ответ на который "НЕТ"
        $neutral = 0; // Сумма баллов вопросов ответ на который "Н\А"
        $arr = []; //Массив индексов вопросов

        array_walk_recursive($data, function($item) use (&$arr){
            $arr[] = $item;
        });
        $questions = AuditQuestion::whereIn('id', $arr)->get();
        
        if(isset($data['result']['positive']))
            foreach($data['result']['positive'] as $item)
                if($questions->contains('id', $item))
                    $positive += $questions->where('id',$item)->first()->getWeight();
        
        if(isset($data['result']['negative']))
            foreach($data['result']['negative'] as $item)
                if($questions->contains('id', $item))
                    $negative += $questions->where('id',$item)->first()->getWeight();

        if(isset($data['result']['neutral']))
            foreach($data['result']['neutral'] as $item)
                if($questions->contains('id', $item))
                    $neutral += $questions->where('id',$item)->first()->getWeight();
        
        $res = (($total-$neutral) > 0) ? $positive/($total-$neutral) : 0;
       
        return round($res*100, 1);
    }



    public function getResponseCount(AuditMaster $master)
    {
        return $master->getResponseCount();
    }



    public function tryClose(AuditMaster $master)
    {
        $questionCount = $master->audit->questions->count();

        $result = $this->getResponseCount($master);

        if($questionCount == $result)
            $master->closeStatus();
    }



    public function create(array $data, )
    {
        $data['point'] = $this->calcPoint($data);

        $data['author_id'] = Auth::id();
        
        $data['positive_count'] = count($data['result']['positive']);

        $data['result'] = json_encode($data['result']);

        $audit = AuditMaster::create($data);

        $this->checkCompleted($audit);

        $this->tryClose($audit);

        return $audit;
    }



    public function update(int $id, array $data)
    {   
        $audit = $this->getById($id);
        
        $data['author_id'] = $audit->author_id ?? Auth::id();

        $data['point'] = $this->calcPoint($data);
        
        $data['positive_count'] = count($data['result']['positive']);

        $data['result'] = json_encode($data['result']);

        $audit->fill($data);

        if($audit->isDirty())
            $audit->save();

        $this->checkCompleted($audit);

        $this->tryClose($audit);

        return $audit;
    }



    public function paginate(array $data)
    {
        $query = AuditMaster::query()->select('audit_masters.*');

        $filter = app()->make(AuditMasterFilter::class, ['queryParams' => $data]);

        $query->withTrashed();

        $query->filter($filter);

        $query->with([
            'audit' => function($q){
                $q->with(['appeal'])->withCount('questions');
            },
            'author', 
            'trafic' => function($q) {
                $q->with(['structure', 'salon', 'manager'],);
            },
            'record' => function($q) {
                $q->select('id','master_id');
            },
        ]);

        $query->orderBy('audit_masters.id', 'DESC');

        $masters = $query->simplePaginate(25);
        
        return $masters;
    }



    public function count(array $data)
    {
        $query = AuditMaster::query();

        $query->addSelect(
            
        );
    }
}