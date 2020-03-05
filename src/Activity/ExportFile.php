<?php
namespace PhpSports\Activity;

use PhpSports\Model\ActivityCollection;

class ExportFile extends BaseFile
{
    public static function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false)
    {
        return self::createInstanceFromFile($fileName, null, 0)->saveToFile($activities, $fileName, $pretty);
    }

    public static function saveToBinary(ActivityCollection $activities, string $format, bool $pretty = false) : string
    {
        return self::createInstanceFromFormat($format, null, 0)->saveToBinary($activities, $pretty);
    }

    public static function saveToArray(ActivityCollection $activities, string $format) : array
    {
        return self::createInstanceFromFormat($format, null, 0)->saveToArray($activities);
    }
}
