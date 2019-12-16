<?php

use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ImportFile;

final class ImportFileTest extends TestCase
{
    private $base_dir;

    public function setUp()
    {
        $this->base_dir = __DIR__ . '/../../samples/';
    }

    public function testImportFileFIT()
    {
        $filePath = $this->base_dir . 'sample_file.fit';
        $activities = ImportFile::createFromFile($filePath);
        echo $filePath . PHP_EOL;
        foreach ($activities as $activity) {
            echo 'TOTAL: ' . $activity->getName() . ' ' . gmdate("H:i:s", $activity->getDurationSeconds()) . ' ' . round($activity->getDistanceMeters() / 1000 , 2) . ' km' . PHP_EOL;
            foreach ($activity->getLaps() as $lap) {
                echo $lap->getName() . ' ' . gmdate("H:i:s", $lap->getDurationSeconds()) . ' ' . round($lap->getDistanceMeters() / 1000, 2) . ' km' . PHP_EOL;
            }
            $this->assertEquals(
                6,
                count($activity->getLaps())
            );
            $this->assertEquals(
                2250,
                $activity->getDurationSeconds()
            );
            $this->assertEquals(
                5070,
                $activity->getDistanceMeters()
            );
        }

        // $obj = json_encode($activity, JSON_PRETTY_PRINT);
        // print_r($obj);
        //
        // $this->assertEquals(
        //     'test',
        //     $activity->getName()
        // );
    }

    public function testImportFileGPX()
    {
        $filePath = $this->base_dir . 'segundo_ejemplo.gpx';
        $activities = ImportFile::createFromFile($filePath);
        echo $filePath . PHP_EOL;
        foreach ($activities as $activity) {
            echo 'TOTAL: ' . $activity->getName() . ' ' . gmdate("H:i:s", $activity->getDurationSeconds()) . ' ' . round($activity->getDistanceMeters() / 1000, 2) . ' km' . PHP_EOL;
            foreach ($activity->getLaps() as $lap) {
                echo $lap->getName() . ' ' . gmdate("H:i:s", $lap->getDurationSeconds()) . ' ' . round($lap->getDistanceMeters() / 1000, 2) . ' km' . PHP_EOL;
            }
            $this->assertEquals(
                1,
                count($activity->getLaps())
            );
            $this->assertEquals(
                7194,
                $activity->getDurationSeconds()
            );
            $this->assertEquals(
                52945,
                $activity->getDistanceMeters()
            );
        }

        //  $obj = json_encode($activity, JSON_PRETTY_PRINT);
        //  print_r($obj);
        //
        // $this->assertEquals(
        //     'test',
        //     $activity->getName()
        // );
    }

}
