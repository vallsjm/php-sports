<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Activity;
use PhpSports\Model\ActivityCollection;

interface ParseReadFileInterface
{

    public function readFromFile(string $fileName) : ActivityCollection;
    public function readOneFromFile(string $fileName) : Activity;

}
