<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseFile extends BaseParse
{
    static public function getFormat()
    {
        return static::FILEFORMAT;
    }

    static public function getType()
    {
        return 'FILE';
    }
}
