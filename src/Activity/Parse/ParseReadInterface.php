<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Activity;
use PhpSports\Model\ActivityCollection;

interface ParseReadInterface {

    public function readFromFile(string $fileName) : ActivityCollection;
    public function readFromBinary(string $data) : ActivityCollection;
    public function readFromArray(array $data) : ActivityCollection;

}
