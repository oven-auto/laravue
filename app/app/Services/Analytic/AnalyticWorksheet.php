<?php

namespace App\Services\Analytic;
use Carbon\Carbon;

Class AnalyticWorksheet
{
    public function toArray($current, $month, $year)
    {
        $arr = [];

        foreach($current as $item)
        {
            $prevMonthCount = 0;
            $prevYearCount = 0;

            if($month->contains('type', $item['type']))
                $prevMonthCount = $month->where('type', $item['type'])->first()['count'];

            if($year->contains('type', $item['type']))
                $prevYearCount = $year->where('type', $item['type'])->first()['count'];

            $arr[] = [
                'name' => $item['name'],
                'count' => $item['count'],
                'percent' => $item['percent'],
                'previos_month' => $prevMonthCount,
                'month_dynamic' => $prevMonthCount ? round( (($item['count'] / $prevMonthCount) - 1) * 100, 2) : ($item['count'] ? 100 : 0),
                'previos_year' => $prevYearCount,
                'year_dynamic' => $prevYearCount ? round( (($item['count'] / $prevYearCount) - 1) * 100, 2) : ($item['count'] ? 100 : 0),
                'border_top' => ($item['border_top']) ?? 0,
                'border_bottom' => ($item['border_bottom']) ?? 0,
            ];
        }
        return $arr;
    }

    public function fasade($data = [], TraficAnalyticInterface $command)
    {
        if(isset($data['closed_month']))
            return $this->isShowClosed($command, $data);

        if(isset($data['created_month']))
            return $this->isShowCreated($command, $data);

        return $command->getArrayAnalytic($data);
    }

    public function isShowClosed($command, array $data)
    {
        $prevMonthData = $data;
        $date = new Carbon($data['closed_month']);
        $prevMonthData['closed_month'] = $date->subMonth()->format('d.m.Y');

        $prevYearData = $data;
        $date = new Carbon($data['closed_month']);
        $prevYearData['closed_month'] = $date->subYear()->format('d.m.Y');

        $current        = $command->getArrayAnalytic($data);
        $prevMont       = $command->getArrayAnalytic($prevMonthData);
        $prevYear       = $command->getArrayAnalytic($prevYearData);

        return $this->toArray($current, $prevMont, $prevYear);
    }

    public function isShowCreated($command, array $data)
    {
        $prevMonthData = $data;
        $date = new Carbon($data['created_month']);
        $prevMonthData['created_month'] = $date->subMonth()->format('d.m.Y');

        $prevYearData = $data;
        $date = new Carbon($data['created_month']);
        $prevYearData['created_month'] = $date->subYear()->format('d.m.Y');

        $current        = $command->getArrayAnalytic($data);
        $prevMont       = $command->getArrayAnalytic($prevMonthData);
        $prevYear       = $command->getArrayAnalytic($prevYearData);

        return $this->toArray($current, $prevMont, $prevYear);
    }
}
