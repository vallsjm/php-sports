<?php
namespace PhpSports\Activity;

use PhpSports\Model\ActivityCollection;

class ImportFile  extends BaseFile
{
    public static function readFromFile(string $fileName, string $format = null) : ActivityCollection
    {
        if (!$format) {
            $format = self::getFileExtension($fileName);
        }
        return self::createInstance($format)->readFromFile($fileName);
    }

    public static function readFromBinary(string $data, string $format) : ActivityCollection
    {
        return self::createInstance($format)->readFromBinary($data);
    }
}
