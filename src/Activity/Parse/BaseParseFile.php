<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseFile
{
    abstract public function readFromFile(string $fileName) : ActivityCollection;
    abstract public function saveToFile(ActivityCollection $activities, string $fileName);

    abstract public function readFromBinary(string $data) : ActivityCollection;
    abstract public function saveToBinary(ActivityCollection $activities) : string;

    public function getFormat()
    {
        return $this::FILETYPE;
    }
}
