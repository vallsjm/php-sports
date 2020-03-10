<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseAPI extends BaseParse
{
    static public function getFormat()
    {
        return static::APIFORMAT;
    }

    static public function getType()
    {
        return 'API';
    }
}
