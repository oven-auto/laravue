<?php

namespace App\Services\Client;
use App\Models\ClientEventStatus;

Class EventClose
{
    private $eventStaus;
    private const FULLY_CLOSE = 'Событие завершено';
    private const INTERVAL_CLOSE = "Событие завершено";

    private $nextDate;

    private function rewrite() : String
    {
        $data = '';
        switch ($this->eventStaus->event->type->slug) {
            case 'everyyear':
                $data = \DateHelp::addYear($this->eventStaus->date_at);
                break;
            case 'everymonth':
                $data = \DateHelp::addMonth($this->eventStaus->date_at);
                break;
            case 'everyweek':
                $data = \DateHelp::addWeek($this->eventStaus->date_at);
                break;
            case 'everyday':
                $data = \DateHelp::addDay($this->eventStaus->date_at);
                break;
            case 'quarterly':
                $data = \DateHelp::addMonth($this->eventStaus->date_at, 3);
                break;
            case 'halfyear':
                $data = \DateHelp::addMonth($this->eventStaus->date_at, 6);
                break;
        }
        $this->nextDate = \DateHelp::format($data);
        return $data;
    }

    private function writeStatus() : void
    {
        $newStatus = '';

        $this->eventStaus->fill([
            'confirm' => 'processed',
            'author_id' => auth()->user()->id,
            'processed_at' => now(),
        ])->save();

        if($this->eventStaus->event->type->slug != 'once')
            $newStatus = $this->eventStaus->event->lastStatus()->create([
                'date_at' => $this->rewrite(),
            ]);

        if(is_object($newStatus))
            $newStatus->lastComment()->create([
                'author_id' => auth()->user()->id,
                'text' => 'Назначена новая дата контроля '.$this->nextDate,
                'event_id' => $this->eventStaus->event_id,
                'client_event_status_id' => $newStatus->id
            ]);
    }

    public function addComment() : String
    {
        $comment = '';
        if($this->eventStaus->event->type->slug == 'once')
            $comment = self::FULLY_CLOSE;
        else
            $comment = str_replace('@', $this->eventStaus->event->lastStatus->date_at->format('d.m.Y'), self::INTERVAL_CLOSE);
        return str_replace('!', date('d.m.Y'), $comment);
    }

    private function writeComment() : void
    {
        $this->eventStaus->lastComment()->create([
            'author_id' => auth()->user()->id,
            'text' => $this->addComment(),
            'event_id' => $this->eventStaus->event_id,
            'client_event_status_id' => $this->eventStaus->id
        ]);
        $this->eventStaus->fresh();
        $this->eventStaus->lastComment->fill(['text' => $this->addComment()])->save();
    }

    private function isWaiting() : bool
    {
        if($this->eventStaus->confirm == 'waiting')
            return true;
        return false;
    }

    public function canIClose()
    {
        if($this->eventStaus->event->resolve == 1 || $this->eventStaus->event->author_id == auth()->user()->id)
            return true;
        return false;
    }

    public function close(int $eventStatusId)
    {
        $this->eventStaus = ClientEventStatus::with('event')->find($eventStatusId);

        if(!$this->isWaiting())
            throw new \Exception('Это событие уже было выполнено!');

        if(!$this->canIClose())
            throw new \Exception('Это событие может закрывать только автор!');

        $this->writeComment();
        $this->writeStatus();

        return $this->eventStaus;
    }
}
