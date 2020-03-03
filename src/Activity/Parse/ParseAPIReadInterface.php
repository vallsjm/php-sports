<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Activity;
use PhpSports\Model\ActivityCollection;

interface ParseAPIReadInterface {

    public function readFromAPI(array $data) : ActivityCollection;

}
