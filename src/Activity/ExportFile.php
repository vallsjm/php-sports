<?php
namespace PhpSports\Activity;

use PhpSports\Model\ActivityCollection;

class ExportFile  extends BaseFile
{
    public static function saveToFile(ActivityCollection $data, string $fileName, bool $pretty = false)
    {
        $format = self::getFileExtension($fileName);
        return self::createInstance($format)->saveToFile($data, $fileName, $pretty);
    }

    public static function saveToBinary(ActivityCollection $data, string $format, bool $pretty = false)
    {
        return self::createInstance($format)->saveToBinary($data, $pretty);
    }
}
