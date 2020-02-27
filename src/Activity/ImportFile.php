<?php
namespace PhpSports\Activity;

use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Athlete;

class ImportFile extends BaseFile
{
    public static function readFromFile(
        string $fileName,
        Athlete $athlete = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        return self::createInstanceFromFile($fileName, $athlete, $options)->readFromFile($fileName);
    }

    public static function readFromBinary(
        string $format,
        string $data,
        Athlete $athlete = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        return self::createInstanceFromFormat($format, $athlete, $options)->readFromBinary($data);
    }
}
