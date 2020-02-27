<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseFile extends BaseParse
{
    static public function getFormat()
    {
        return $this::FILETYPE;
    }

    static public function getSource()
    {
        return 'FILE';
    }
}
