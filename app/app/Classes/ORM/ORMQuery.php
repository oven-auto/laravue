<?php

namespace App\Classes\ORM;

Class ORMQuery
{
    private $queryArr;

    public function clearQuery(string $key)
    {
        unset($this->queryArr[$key]);
    }



    public function getLimit()
    {
        if($this->queryArr['limit'])
            return 'LIMIT '.$this->queryArr['limit'];
    }



    public function getWhere()
    {
        if(!isset(($this->queryArr['where'])))
            return;

        $str[] = 'where';

        if($this->queryArr['where'])
            foreach($this->queryArr['where'] as $key => $item)
                if($key == 0)
                    $str[] = $item[0] . ' '.$item[1] . ' '. $item[2];
                else
                    $str[] = $item[3] . ' ' .$item[0] . ' '.$item[1] . ' '. $item[2];
        
        $str = implode(' ', $str);

        return $str;
    }



    public function getSelect()
    {
        $arr = $this->queryArr['select'] ?? ['*'];

        return implode(', ', $arr);
    }



    public function getOrder()
    {
        if(!isset($this->queryArr['order']))
            return;
        
        foreach($this->queryArr['order'] as $item)
            $str[] = $item[0] . ' ' . $item[1];
        return 'order by ' . implode(', ', $str);
    }



    public function getJoin()
    {
        if(!isset($this->queryArr['join']))
            return;

        foreach($this->queryArr['join'] as $item)
            $str[] = implode(' ', $item);
        
        return implode(' ', $str);
    }



    public function where(array $arr)
    {
        $col        = $arr[0];
        $operand    = count($arr) == 3 ? $arr[1] : '=';
        $val        = count($arr) == 3 ? $arr[2] : $arr[1];
        $logic      = 'and';

        $arr = $this->queryArr['where'] ?? [];

        array_push($arr, [$col, $operand, $val, $logic]);

        $this->queryArr['where'] = $arr;

        return $this;
    }



    public function limit($val)
    {   
        $val = $val[0];

        $this->queryArr['limit'] = $val;

        return $this;
    }



    public function select(array $arr)
    {
        $data = $this->queryArr['select'] ?? [];
        
        foreach($arr as $item)
            array_push($data, $item);

        $this->queryArr['select'] = $data;

        return $this;
    }



    public function orderBy(array $arr)
    {
        $col = $arr[0];

        $type = $arr[1] ?? 'ASC';

        $data = $this->queryArr['order'] ?? [];

        array_push($data, [$col, $type]);

        $this->queryArr['order'] = $data;
    }



    public function leftJoin(array $arr)
    {
        $table = $arr[0];
        $colOut = $arr[1];
        $operand = $arr[2];
        $colIn = $arr[3];

        $data = $this->queryArr['join'] ?? [];

        array_push($data, ['LEFT JOIN', $table, 'on', $colOut, $operand, $colIn]);

        $this->queryArr['join'] = $data;
    }
}