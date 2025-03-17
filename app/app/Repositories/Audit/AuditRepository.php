<?php

Namespace App\Repositories\Audit;

use App\Models\Audit\Audit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

Class AuditRepository
{
    /**
     * Получить список аудитов по шаблону [id, name]
     */
    public function getAll(array $data) : Collection
    {
        $query = Audit::query();

        if(isset($data['trash']))
            $query->onlyTrashed();

        $audits = $query->get();
        
        return $audits;
    }



    /**
     * Создать аудит
     */
    public function create(array $data) : Audit
    {
        $audit = DB::transaction(function() use($data){
            $data['author_id'] = auth()->user()->id;
        
            $audit = Audit::create(Arr::except($data, 'chanels'));

            $audit->chanels()->sync($data['chanels']);

            return $audit;
        }, 3);
        
        return $audit;
    }



    /**
     * Изменить аудит
     */
    public function update(int $id, array $data) : Audit
    {
        $audit = DB::transaction(function() use($id, $data){
            $audit = Audit::findOrFail($id);

            $audit->fill($data);

            if($audit->isDirty())
                $audit->save();

            $audit->chanels()->sync($data['chanels']);

            return $audit;
        }, 3);
        
        return $audit;
    }



    /**
     * Получить аудит по ID
     */
    public function getById(int $id) : Audit
    {
        $audit = Audit::with('chanels')->findOrFail($id);

        return $audit;
    }



    /**
     * Удалить аудит (все связи каскадом)
     */
    public function delete(int $id) : Audit
    {
        $res = DB::transaction(function() use ($id){
            $audit = Audit::findOrFail($id);

            $res = $audit->replicate();
            
            $audit->delete();

            return $res;
        }, 3);
        
        return $res;
    }



    public function restore(int $id) : Audit
    {
        $audit = Audit::onlyTrashed()->findOrFail($id);

        $audit->restore();

        return $audit;
    }
}