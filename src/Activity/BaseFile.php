<?php
namespace PhpSports\Activity;

abstract class BaseFile
{
    private static $instance;

    public static function getFileTypes() : array
    {
        $classNames = [
            'PhpSports\Activity\Parse\ParseFile\ParseFileFIT',
            'PhpSports\Activity\Parse\ParseFile\ParseFileGPX',
            'PhpSports\Activity\Parse\ParseFile\ParseFileTCX'
            // 'PhpSports\Activity\Parse\ParseFile\ParseFileKNH'
        ];

        $formats = [];
        foreach ($classNames as $value) {
            $filetype = constant($value . '::FILETYPE');
            $formats[$filetype] = $value;
        }

        return $formats;
    }

    public static function getFileExtension(string $fileName) : string
    {
		return strtoupper(pathinfo($fileName, \PATHINFO_EXTENSION));
    }

    public static function createInstance($format)
    {
        $fileTypes     = self::getFileTypes();

        if (!isset($fileTypes[$format])) {
            throw new \Exception("Format {$format} not suported.");
        }

        if (!is_null( self::$instance )) {
            if (self::$instance->getFormat() != $format) {
                self::$instance = null;
            }
        }

        if (is_null( self::$instance )) {
            if (isset($fileTypes[$format])) {
                self::$instance = new $fileTypes[$format]();
            }
        }

        return self::$instance;
    }
}
