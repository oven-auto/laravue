<?php

namespace App\Classes\LadaDNM;

use App\Models\Client;
use App\Models\Trafic;
use App\Models\Worksheet;

class DNMFactory
{
    public static function factory(Worksheet | Trafic | Client $object)
    {
        $className = (explode('\\', get_class($object)));
        $className = (array_pop($className));

        switch ($className) {
            case 'Trafic':
                return new DNMTrafic($object);
                break;
            case 'Client':
                return new DNMClient($object);
                break;
            case 'Worksheet':
                return new DNMWorksheet($object);
                break;
            default:
                break;
        }
    }
}
