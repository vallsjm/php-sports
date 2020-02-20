<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseFile extends BaseParse
{
    public function getFormat()
    {
        return $this::FILETYPE;
    }

    public function getSource()
    {
        return 'FILE';
    }
}
