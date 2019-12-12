<?php

namespace PhpSports\Import;

use PhpSports\Import\BaseParse;
use PhpSports\Model\ActivitiesArray;

abstract class BaseParseFile extends BaseParse
{
    abstract public function loadFromFile(string $fileName) : ActivitiesArray;
    abstract public function saveToFile(ActivitiesArray $activities, string $fileName);

    abstract public function loadFromBinary(string $data) : ActivitiesArray;
    abstract public function saveToBinary() : string;

    public function getFormat()
    {
        self::FILETYPE;
    }
}
