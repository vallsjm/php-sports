<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Activity;
use PhpSports\Model\ActivityCollection;

interface ParseFileSaveInterface {

    public function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false);
    public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string;

}
