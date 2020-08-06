<?php
namespace PhpSports\Activity;

use PhpSports\Model\AthleteStatus;
use PhpSports\Analyzer\AnalyzerInterface;
use PhpSports\Activity\Parse\BaseParseFile;
use InvalidArgumentException;

abstract class BaseFile implements AnalyzerInterface
{
    public static function getFileExtension(string $fileName) : string
    {
        return strtoupper(pathinfo($fileName, \PATHINFO_EXTENSION));
    }

    public static function createInstanceFromFile(
        string $fileName = null,
        AthleteStatus $athleteSatus = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : BaseParseFile {
        $instance  = null;
        $format    = self::getFileExtension($fileName);
        $className = 'PhpSports\Activity\Parse\ParseFile\ParseFile' . $format;
        if (class_exists($className)) {
            $instance = $className::createInstance($athleteSatus, $options);
        } else {
            throw new InvalidArgumentException("Format {$format} not suported.");
        }

        return $instance;
    }

    public static function createInstanceFromFormat(
        string $format = null,
        AthleteStatus $athleteSatus = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : BaseParseFile {
        $instance  = null;
        $className = 'PhpSports\Activity\Parse\ParseFile\ParseFile' . $format;
        if (class_exists($className)) {
            $instance = $className::createInstance($athleteSatus, $options);
        } else {
            throw new InvalidArgumentException("Format {$format} not suported.");
        }

        return $instance;
    }
}
