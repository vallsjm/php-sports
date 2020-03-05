<?php
namespace PhpSports\Activity;

use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Athlete;

class ImportAPI extends BaseAPI
{
    public static function readFromArray(
        string $apiName,
        array $data,
        Athlete $athlete = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        return self::createInstanceFromAPI($apiName, $athlete, $options)->readFromArray($data);
    }
    public static function readFromBinary(
        string $apiName,
        string $data,
        Athlete $athlete = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        return self::createInstanceFromAPI($apiName, $athlete, $options)->readFromBinary($data);
    }

    public static function readFromFile(
        string $apiName,
        string $fileName,
        Athlete $athlete = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        return self::createInstanceFromAPI($apiName, $athlete, $options)->readFromFile($fileName);
    }

}
