<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Activity;
use PhpSports\Model\ActivityCollection;

interface ParseSaveArrayInterface
{

    public function saveToArray(ActivityCollection $activities) : array;
    public function saveOneToArray(Activity $activity) : array;

}
