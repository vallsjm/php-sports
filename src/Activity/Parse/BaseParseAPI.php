<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseAPI extends BaseParse
{
    static public function getFormat()
    {
        return static::APITYPE;
    }

    static public function getSource()
    {
        return 'API';
    }
}
