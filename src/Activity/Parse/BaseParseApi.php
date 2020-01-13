<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseApi extends BaseParse
{
    abstract public function readFromBinary(array $data, ActivityCollection $activities) : ActivityCollection;

    public function getFormat()
    {
        return $this::APITYPE;
    }

    public function getSource()
    {
        return 'API';
    }
}
