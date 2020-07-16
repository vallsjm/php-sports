<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Activity;
use PhpSports\Model\ActivityCollection;

interface ParseReadArrayInterface
{

    public function readFromArray(array $data) : ActivityCollection;
    public function readOneFromArray(array $data) : Activity;

}
