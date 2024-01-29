<?php

namespace App\Repositories\Worksheet\SubAction;

use App\Models\SubAction;
use App\Services\Worksheet\WorksheetUser;
use App\Models\User;
use \Illuminate\Database\Eloquent\Collection;

/**
 * РЕПОЗИТОРИЙ ПОДЗАДАЧИ
 * 1 - ПОЛУЧИТЬ ВСЕ ПОДЗАДАЧИ ИЗ ВЫБРАННОГО РЛ
 * 2 - СОХРАНИТЬ ПОДЗАДАЧУ
 * 3 - ЗАКРЫТЬ ПОДЗАДАЧУ
 * 4 - ДОБАВИТЬ ИСПОЛНИТЕЛЕЙ В ПОДЗАДАЧУ
 * 5 - УДАЛИТЬ ИСПОЛНИТЕЛЯ ИЗ ПОДЗАДАЧИ
 * 6 - ОТЧИТАТЬСЯ ЗА ПОДЗАДАЧУ
 * 7 - СНЯТЬ ОТМЕТКУ ОБ ОТЧЕТЕ
 * 8 - ЗАПИСАТЬ КОММЕНТАРИЙ В ПОДЗАДАЧУ
 * 9 - ПОЛУЧИТЬ ВСЕ КОММЕНТАРИИ ПОДЗАДАЧИ В ВИДЕ МАССИВА
 *
 * 16-01-2024
 *
 */
Class SubActionRepository
{
    /**
     * 1 - ПОЛУЧИТЬ ВСЕ ПОДЗАДАЧИ ИЗ ВЫБРАННОГО РЛ
     * @param int $worksheetId
     * @return Collection
     */
    public function getAllByWorksheetId(int $worksheetId) : Collection
    {
        $result = SubAction::where('worksheet_id', $worksheetId)->orderBy('id','DESC')->get();

        return $result;
    }



    /**
     * 2 - СОХРАНИТЬ ПОДЗАДАЧУ
     * @param SubAction $subAction
     * @param array $data [title => string, worksheet_id => int, ?executors => array, comment => string]
     * @return void
     */
    public function save(SubAction $subAction, array $data) : void
    {
        if(!$subAction->author_id)
            $subAction->author_id = auth()->user()->id;

        if($subAction->title && $subAction->title != $data['title'])
            $this->writeComment($subAction, 'Изменено название задачи, новое название '.$data['title']);

        $subAction->fill([
            'worksheet_id' => $data['worksheet_id'],
            'title' => $data['title'],
        ])->save();

        if(!isset($data['executors']))
            $data['executors'] = [auth()->user()->id];

        if(isset($data['executors']))
            $this->setExecutors($subAction, $data['executors']);

        if(isset($data['comment']))
            $this->writeComment($subAction, $data['comment']);

        $subAction->refresh();

        $subAction->load(['comments', 'executors']);
    }



    /**
     * 3 - ЗАКРЫТЬ ПОДЗАДАЧУ
     * @param SubAction $subAction
     * @return void
     */
    public function closeAction(SubAction $subAction) : void
    {
        if($subAction->author_id == auth()->user()->id)
            $subAction->close();
    }



    /**
     * 4 - ДОБАВИТЬ ИСПОЛНИТЕЛЕЙ В ПОДЗАДАЧУ
     * @param SubAction $subAction
     * @param array $executors [executors => array(int, ..., int)]
     * @return void
     */
    public function setExecutors(SubAction $subAction, array $executorsArray) : void
    {
        $originalExecutorsArray = $subAction->executors->pluck('id')->toArray();

        $executorsArray[] = auth()->user()->id;

        $executorsArray = array_merge($executorsArray, $originalExecutorsArray);

        $subAction->executors()->sync($executorsArray);

        WorksheetUser::attach(
            \App\Models\Worksheet::findOrFail($subAction->worksheet_id),
            \App\Models\User::whereIn('id', $executorsArray)->get()
        );

        $subAction->load('executors');
    }



    /**
     * 5 - УДАЛИТЬ ИСПОЛНИТЕЛЯ ИЗ ПОДЗАДАЧИ
     * @param SubAction $subAction
     * @param int $executorId executor => int]
     * @return void
     */
    public function removeExecutor(SubAction $subAction, int $executorId) : void
    {
        if($subAction->author_id != $executorId)
            $subAction->executors()->detach($executorId);
    }



    /**
     * 6 - ОТЧИТАТЬСЯ ЗА ПОДЗАДАЧУ
     * @param SubAction $subAction
     * @param int $reporterId
     * @return void
     */
    public function report(SubAction|int $subAction, int|User $reporterId) : void
    {
        if(is_numeric($subAction))
            $subAction = SubAction::findOrFail($subAction);

        if(is_numeric($reporterId))
            $reporterId = User::findOrFail($reporterId);

        if(!$subAction->executors->contains('id', $reporterId->id))
            throw new \Exception('Вы не являетесь участником задачи');

        $subAction->reporters()->attach($reporterId->id);

        $this->removeExecutor($subAction, $reporterId->id);
    }



    /**
     * 7 - СНЯТЬ ОТМЕТКУ ОБ ОТЧЕТЕ
     * @param SubAction $subAction
     * @param int $reporterId
     * @return void
     */
    public function deport(SubAction $subAction, int $reporterId) : void
    {
        $subAction->reporters()->detach($reporterId);
        $this->setExecutors($subAction, [$reporterId]);
    }



    /**
     * 8 - ЗАПИСАТЬ КОММЕНТАРИЙ В ПОДЗАДАЧУ
     * @param SubAction $subAction
     * @param string $text
     * @return void
     */
    public function writeComment(SubAction $subAction, string $text) : void
    {
        $subAction->comments()->create([
            'text' => $text,
            'author_id' => auth()->user()->id,
        ]);
    }



    /**
     * 9 - ПОЛУЧИТЬ ВСЕ КОММЕНТАРИИ ПОДЗАДАЧИ В ВИДЕ МАССИВА
     * @param SubAction $subAction
     * @return  \Illuminate\Support\Collection
     */
    public function getSubActionComments(SubAction $subAction) : \Illuminate\Support\Collection
    {
        return $subAction->comments->map(fn($item) => [
            'text' => $item->text,
            'id' => $item->id,
            'author' => $item->author->cut_name,
            'created_at' => $item->created_at->format('d.m.Y (H:i)'),
        ]);
    }
}
