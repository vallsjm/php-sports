<?php
namespace PhpSports\Activity;

use PhpSports\Model\ActivityCollection;

class ImportFile  extends BaseFile
{
    public static function readFromFile(string $fileName, string $format = null, ActivityCollection $activities = null) : ActivityCollection
    {
        if (!$format) {
            $format = self::getFileExtension($fileName);
        }
        if (!$activities) {
            $activities = new ActivityCollection();
        }
        return self::createInstance($format)->readFromFile($fileName, $activities);
    }

    public static function readFromBinary(string $data, string $format, ActivityCollection $activities = null) : ActivityCollection
    {
        if (!$activities) {
            $activities = new ActivityCollection();
        }
        return self::createInstance($format)->readFromBinary($data, $activities);
    }
}
