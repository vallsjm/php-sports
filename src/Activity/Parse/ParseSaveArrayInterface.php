<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

interface ParseSaveArrayInterface
{

    public function saveToArray(ActivityCollection $activities) : array;

}
