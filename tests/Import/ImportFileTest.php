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

    public function consoleLog($text)
    {
        fwrite(STDERR, $text . PHP_EOL);
    }

    public function testImportFileFIT()
    {
        $timeStart = microtime(true);
        $filePath = $this->base_dir . 'sample_file.fit';
        $activities = ImportFile::createFromFile($filePath);
        $timeEnd = microtime(true);

        $this->consoleLog('FILE:' . $filePath);
        $this->consoleLog('TIME: '. round($timeEnd - $timeStart, 4) . ' s.' . PHP_EOL);
        foreach ($activities as $activity) {
            foreach ($activity->getLaps() as $lap) {
                $this->consoleLog($lap->getName() . ' ' . gmdate("H:i:s", $lap->getDurationSeconds()) . ' ' . round($lap->getDistanceMeters() / 1000, 2) . ' km');
            }
            $this->consoleLog(PHP_EOL . 'TOTAL: ' . $activity->getName() . ' ' . gmdate("H:i:s", $activity->getDurationSeconds()) . ' ' . round($activity->getDistanceMeters() / 1000 , 2) . ' km ');
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
        $timeStart = microtime(true);
        $filePath = $this->base_dir . 'segundo_ejemplo.gpx';
        $activities = ImportFile::createFromFile($filePath);
        $timeEnd = microtime(true);

        $this->consoleLog('FILE:' . $filePath);
        $this->consoleLog('TIME: '. round($timeEnd - $timeStart, 4) . ' s.' . PHP_EOL);
        foreach ($activities as $activity) {
            foreach ($activity->getLaps() as $lap) {
                $this->consoleLog($lap->getName() . ' ' . gmdate("H:i:s", $lap->getDurationSeconds()) . ' ' . round($lap->getDistanceMeters() / 1000, 2) . ' km');
            }
            $this->consoleLog(PHP_EOL . 'TOTAL: ' . $activity->getName() . ' ' . gmdate("H:i:s", $activity->getDurationSeconds()) . ' ' . round($activity->getDistanceMeters() / 1000 , 2) . ' km ');
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
