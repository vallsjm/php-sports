<?php
namespace PhpSports\Activity;

use PhpSports\Model\ActivityCollection;

class ExportFile  extends BaseFile
{
    public static function saveToFile(ActivityCollection $data, string $fileName)
    {
        $format = self::getFileExtension($fileName);
        return self::createInstance($format)->saveToFile($data, $fileName);
    }

    public static function saveToBinary(ActivityCollection $data, string $format)
    {
        return self::createInstance($format)->saveToBinary($data);
    }
}
