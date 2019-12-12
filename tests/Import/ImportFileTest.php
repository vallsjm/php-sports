<?php

use PHPUnit\Framework\TestCase;
use PhpSports\Import\ImportFile;

final class ImportFileTest extends TestCase
{
    private $base_dir;

    public function setUp()
    {
        $this->base_dir = __DIR__ . '/../../samples/';
    }

    public function testImportFileFit()
    {
        $filePath = $this->base_dir . 'sample_file.fit';
        $activity = ImportFile::createFromFile($filePath);

        $obj = json_encode($activity, JSON_PRETTY_PRINT);
        print_r($obj);

        $this->assertEquals(
            'test',
            $activity->getName()
        );
    }
}
