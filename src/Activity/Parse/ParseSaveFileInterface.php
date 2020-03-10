<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

interface ParseSaveFileInterface
{

    public function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false);

}
