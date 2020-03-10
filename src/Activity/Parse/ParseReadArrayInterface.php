<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

interface ParseReadArrayInterface
{

    public function readFromArray(array $data) : ActivityCollection;

}
