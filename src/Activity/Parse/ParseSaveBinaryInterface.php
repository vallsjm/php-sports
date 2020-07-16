<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Activity;
use PhpSports\Model\ActivityCollection;

interface ParseSaveBinaryInterface
{

    public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string;
    public function saveOneToBinary(Activity $activity, bool $pretty = false) : string;

}
