<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

interface ParseReadBinaryInterface
{

    public function readFromBinary(string $data) : ActivityCollection;

}
