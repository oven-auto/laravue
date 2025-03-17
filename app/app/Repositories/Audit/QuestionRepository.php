<?php

namespace App\Repositories\Audit;

use App\Models\Audit\AuditAnswer;
use App\Models\Audit\AuditQuestion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

Class QuestionRepository
{
    public function get(array $data) : Collection
    {
        $query = AuditQuestion::query();

        if(isset($data['audit_id']))
            $query->where('audit_id', $data['audit_id']);

        if(isset($data['trash']))
            $query->onlyTrashed();

        $questions = $query->get();

        return $questions;
    }



    public function getById(int $id) : AuditQuestion
    {
        $question = AuditQuestion::findOrFail($id);

        return $question;
    }



    public function create(array $data) : AuditQuestion
    {
        $question = DB::transaction(function() use ($data) {
            $data['author_id'] = auth()->user()->id;

            $data['sort'] = (AuditQuestion::query()->where('audit_id', $data['audit_id'])->max('sort') ?? 0) + 1;
            
            $question = AuditQuestion::create(Arr::except($data, ['answers']));
           
            foreach($data['answers'] as $key => $item)
            {   
                if(in_array($key, AuditAnswer::ANSWER_TYPES) && $item != '')
                    $question->answers()->create([
                        'question_id' => $question->id,
                        'author_id' => auth()->user()->id,
                        'type' => $key,
                    ]);
            }

            return $question;
        }, 3);

        return $question;
    }



    public function update(int $id, array $data) : AuditQuestion
    {
        $question = AuditQuestion::findOrFail($id);

        $question->fill($data);

        if($question->isDirty())
            $question->save();

        return $question;
    }



    public function delete(int $id) : AuditQuestion
    {
        $question = AuditQuestion::findOrFail($id);

        $res = $question->replicate();

        $question->delete();

        return $res;
    }



    public function restore(int $id) : AuditQuestion
    {
        $question = AuditQuestion::onlyTrashed()->findOrFail($id);

        $question->restore();

        return $question;
    }
}