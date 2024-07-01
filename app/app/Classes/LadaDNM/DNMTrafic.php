<?php

namespace App\Classes\LadaDNM;

use App\Models\Trafic;

class DNMTrafic
{
    private $dnmService;

    private $obj;

    public function __construct(Trafic $obj)
    {
        $this->dnmService = new DNM();

        $this->obj = $obj;
    }



    public function handler()
    {
    }
}
