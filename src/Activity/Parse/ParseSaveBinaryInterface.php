<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

interface ParseSaveBinaryInterface
{

    public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string;

}
