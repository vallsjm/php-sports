<?php
namespace PhpSports\Activity;

use PhpSports\Model\ActivityCollection;

class ExportFile  extends BaseFile
{
    public static function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false)
    {
        $format = self::getFileExtension($fileName);
        return self::createInstance($format)->saveToFile($activities, $fileName, $pretty);
    }

    public static function saveToBinary(ActivityCollection $activities, string $format, bool $pretty = false)
    {
        return self::createInstance($format)->saveToBinary($activities, $pretty);
    }
}
