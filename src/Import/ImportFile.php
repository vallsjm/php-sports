<?php
namespace PhpSports\Import;

class ImportFile
{
    private static $instance;

    public static function getFileTypes() : array
    {
        $classNames = [
            'PhpSports\Import\Parse\ParseFile\ParseFileFIT'
            // 'PhpSports\Import\Parse\ParseFile\ParseFileTCX',
            // 'PhpSports\Import\Parse\ParseFile\ParseFileGPX',
            // 'PhpSports\Import\Parse\ParseFile\ParseFileKNH'
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

    public static function createFromFile(string $fileName, string $format = null)
    {
        if (!$format) {
            $format = self::getFileExtension($fileName);
        }
        $fileTypes = self::getFileTypes();

        if (!isset($fileTypes[$format])) {
            throw new \Exception("File *.{$format} not suported.");
        }

        if (!is_null( self::$instance )) {
            if (self::$instance->getFormat() != $format) {
                unset(self::$instance);
                self::$instance = null;
            }
        }
        if (is_null( self::$instance )) {
            if (isset($fileTypes[$format])) {
                self::$instance = new $fileTypes[$format]();
            }
        }

        return self::$instance->loadFromFile($fileName);
    }

    public static function createFromBinary(string $data, string $format)
    {
        $fileTypes     = self::getFileTypes();

        if (!isset($fileTypes[$format])) {
            throw new \Exception("Format {$format} not suported.");
        }

        if (!is_null( self::$instance )) {
            if (self::$instance->getFormat() != $format) {
                unset(self::$instance);
                self::$instance = null;
            }
        }
        if (is_null( self::$instance )) {
            if (isset($fileTypes[$format])) {
                self::$instance = new $fileTypes[$format]();
            }
        }

        return self::$instance->loadFromBinary($data);
    }
}
