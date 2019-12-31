<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseApi
{
    abstract public function readFromBinary(array $data, ActivityCollection $activities) : ActivityCollection;

    public function getFormat()
    {
        return $this::APITYPE;
    }
}
