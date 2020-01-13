<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseFile extends BaseParse
{
    abstract public function readFromFile(string $fileName, ActivityCollection $activities) : ActivityCollection;
    abstract public function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false);

    abstract public function readFromBinary(string $data, ActivityCollection $activities) : ActivityCollection;
    abstract public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string;

    public function getFormat()
    {
        return $this::FILETYPE;
    }

    public function getSource()
    {
        return 'FILE';
    }
}
