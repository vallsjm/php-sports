<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseFile extends BaseParse
{
    static public function getFormat()
    {
        return static::FILETYPE;
    }

    static public function getSource()
    {
        return 'FILE';
    }
}
