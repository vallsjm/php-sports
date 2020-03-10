<?php
namespace PhpSports\Activity;

use PhpSports\Activity\Parse\ParseSaveFileInterface;
use PhpSports\Activity\Parse\ParseSaveBinaryInterface;
use PhpSports\Activity\Parse\ParseSaveArrayInterface;
use PhpSports\Model\ActivityCollection;

class ExportFile extends BaseFile
{
    public static function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false)
    {
        $instance = self::createInstanceFromFile($fileName, null, 0);
        if ($instance instanceof ParseSaveFileInterface) {
            return $instance->saveToFile($activities, $fileName, $pretty);
        } else {
            throw new \InvalidArgumentException("save to file not suported.");
        }
    }

    public static function saveToBinary(ActivityCollection $activities, string $format, bool $pretty = false) : string
    {
        $instance = self::createInstanceFromFormat($format, null, 0);
        if ($instance instanceof ParseSaveBinaryInterface) {
            return $instance->saveToBinary($activities, $pretty);
        } else {
            throw new \InvalidArgumentException("save to binary not suported.");
        }
    }

    public static function saveToArray(ActivityCollection $activities, string $format) : array
    {
        $instance = self::createInstanceFromFormat($format, null, 0);
        if ($instance instanceof ParseSaveArrayInterface) {
            return $instance->saveToArray($activities);
        } else {
            throw new \InvalidArgumentException("save to array not suported.");
        }
    }
}
