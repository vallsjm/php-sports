<?php

namespace PhpSports\Import\Parse;

use PhpSports\Import\Parse\BaseParse;
use PhpSports\Model\ActivityCollection;

abstract class BaseParseFile extends BaseParse
{
    abstract public function loadFromFile(string $fileName) : ActivityCollection;
    abstract public function saveToFile(ActivityCollection $activities, string $fileName);

    abstract public function loadFromBinary(string $data) : ActivityCollection;
    abstract public function saveToBinary() : string;

    public function getFormat()
    {
        self::FILETYPE;
    }
}
