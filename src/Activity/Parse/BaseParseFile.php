<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseFile extends BaseParse
{
    public function getFormat()
    {
        return static::FILEFORMAT;
    }

    public function getType()
    {
        return 'FILE';
    }
}
