<?php
namespace PhpSports\Activity;

use PhpSports\Activity\Parse\ParseReadFileInterface;
use PhpSports\Activity\Parse\ParseReadBinaryInterface;
use PhpSports\Activity\Parse\ParseReadArrayInterface;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\AthleteStatus;

class ImportFile extends BaseFile
{
    public static function readFromFile(
        string $fileName,
        AthleteStatus $athleteStatus = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        $instance = self::createInstanceFromFile($fileName, $athleteStatus, $options);
        if ($instance instanceof ParseReadFileInterface) {
            return $instance->readFromFile($fileName);
        } else {
            throw new \InvalidArgumentException("read from file not suported.");
        }
    }

    public static function readFromBinary(
        string $format,
        string $data,
        AthleteStatus $athleteStatus = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        $instance = self::createInstanceFromFormat($format, $athleteStatus, $options);
        if ($instance instanceof ParseReadBinaryInterface) {
            return $instance->readFromBinary($data);
        } else {
            throw new \InvalidArgumentException("read from binary not suported.");
        }
    }

    public static function readFromArray(
        string $format,
        array $data,
        AthleteStatus $athleteStatus = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        $instance = self::createInstanceFromFormat($format, $athleteStatus, $options);
        if ($instance instanceof ParseReadArrayInterface) {
            return $instance->readFromArray($data);
        } else {
            throw new \InvalidArgumentException("read from array not suported.");
        }
    }

}
