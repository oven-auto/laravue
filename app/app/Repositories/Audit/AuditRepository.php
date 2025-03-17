<?php

Namespace App\Repositories\Audit;

use App\Models\Audit\Audit;
use Illuminate\Database\Eloquent\Collection;

Class AuditRepository
{
    /**
     * Получить список аудитов по шаблону [id, name]
     */
    public function getOnlyNames(array $data) : Collection
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
        $data['author_id'] = auth()->user()->id;
        
        $audit = Audit::create($data);

        return $audit;
    }



    /**
     * Изменить аудит
     */
    public function update(int $id, array $data) : Audit
    {
        $audit = Audit::findOrFail($id);

        $audit->fill($data);

        if($audit->isDirty())
            $audit->save();

        return $audit;
    }



    /**
     * Получить аудит по ID
     */
    public function getById(int $id) : Audit
    {
        $audit = Audit::findOrFail($id);

        return $audit;
    }



    /**
     * Удалить аудит (все связи каскадом)
     */
    public function delete(int $id) : Audit
    {
        $audit = Audit::findOrFail($id);

        $res = $audit->replicate();
        
        $audit->delete();

        return $res;
    }



    public function restore(int $id) : Audit
    {
        $audit = Audit::onlyTrashed()->findOrFail($id);

        $audit->restore();

        return $audit;
    }
}