<?php
namespace PhpSports\Activity;

use PhpSports\Activity\Parse\ParseReadFileInterface;
use PhpSports\Activity\Parse\ParseReadBinaryInterface;
use PhpSports\Activity\Parse\ParseReadArrayInterface;
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
        $instance = self::createInstanceFromFile($fileName, $athlete, $options);
        if ($instance instanceof ParseReadFileInterface) {
            return $instance->readFromFile($fileName);
        } else {
            throw new \InvalidArgumentException("read from file not suported.");
        }
    }

    public static function readFromBinary(
        string $format,
        string $data,
        Athlete $athlete = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        $instance = self::createInstanceFromFormat($format, $athlete, $options);
        if ($instance instanceof ParseReadBinaryInterface) {
            return $instance->readFromBinary($data);
        } else {
            throw new \InvalidArgumentException("read from binary not suported.");
        }
    }

    public static function readFromArray(
        string $format,
        array $data,
        Athlete $athlete = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : ActivityCollection
    {
        $instance = self::createInstanceFromFormat($format, $athlete, $options);
        if ($instance instanceof ParseReadArrayInterface) {
            return $instance->readFromArray($data);
        } else {
            throw new \InvalidArgumentException("read from array not suported.");
        }
    }

}
