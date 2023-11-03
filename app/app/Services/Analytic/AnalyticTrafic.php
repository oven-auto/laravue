<?php

namespace App\Services\Analytic;
use Carbon\Carbon;

Class AnalyticTrafic
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
                'month_dynamic' => $prevMonthCount ? round( (($item['count'] / $prevMonthCount) - 1) * 100, 2) : 0,
                'previos_year' => $prevYearCount,
                'year_dynamic' => $prevYearCount ? round( (($item['count'] / $prevYearCount) - 1) * 100, 2) : 0,
                'border_top' => ($item['border_top']) ?? 0,
                'border_bottom' => ($item['border_bottom']) ?? 0,
            ];
        }
        return $arr;
    }

    public function fasade($data = [], TraficAnalyticInterface $command)
    {
        if($this->isShowMonth($data))
        {
            $prevMonthData = $data;
            $date = new Carbon($data['show_month']);
            $prevMonthData['show_month'] = $date->subMonth()->format('d.m.Y');

            $prevYearData = $data;
            $date = new Carbon($data['show_month']);
            $prevYearData['show_month'] = $date->subYear()->format('d.m.Y');

            $current        = $command->getArrayAnalytic($data);
            $prevMont       = $command->getArrayAnalytic($prevMonthData);
            $prevYear       = $command->getArrayAnalytic($prevYearData);

            return $this->toArray($current, $prevMont, $prevYear);
        }

        $current        = $command->getArrayAnalytic($data);
        return $current;
    }

    public function isShowMonth(array $data)
    {
        if(isset($data['show_month']))
            return 1;
        return 0;
    }
}
