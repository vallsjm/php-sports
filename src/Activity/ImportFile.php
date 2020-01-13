<?php
namespace PhpSports\Activity;

use PhpSports\Model\ActivityCollection;

class ImportFile extends BaseFile
{
    public static function readFromFile(string $fileName, string $format = null, ActivityCollection $activities = null) : ActivityCollection
    {
        return self::createInstance($fileName, $format)->readFromFile($fileName, $activities);
    }

    public static function readFromBinary(string $data, string $format, ActivityCollection $activities = null) : ActivityCollection
    {
        return self::createInstance(null, $format)->readFromBinary($data, $activities);
    }
}
