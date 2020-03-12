<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseAPI extends BaseParse
{
    public function getFormat()
    {
        return static::APIFORMAT;
    }

    public function getType()
    {
        return 'API';
    }
}
