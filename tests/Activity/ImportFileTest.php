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

    private function consoleLog($text)
    {
        fwrite(STDERR, $text . PHP_EOL);
    }

    private function responseAnalysis(
        $activities,
        $filePath,
        $timeStart,
        $timeEnd,
        $asserts = []
    )
    {
        $this->consoleLog(PHP_EOL . PHP_EOL . 'FILE:' . $filePath);
        $this->consoleLog('TIME: ' . round($timeEnd - $timeStart, 4) . ' s.' . PHP_EOL);
        foreach ($activities as $activity) {
            foreach ($activity->getLaps() as $lap) {
                $this->consoleLog($lap->getName() . ' ' . $lap->getStartedAt()->format('Y-m-d H:i:s') . ' ' . gmdate("H:i:s", $lap->getDurationSeconds()) . ' ' . round($lap->getDistanceMeters() / 1000, 2) . ' km');
            }
            $this->consoleLog(PHP_EOL . 'TOTAL: ' . $activity->getName() . ' ' . $activity->getStartedAt()->format('Y-m-d H:i:s') . ' ' . gmdate("H:i:s", $activity->getDurationSeconds()) . ' ' . round($activity->getDistanceMeters() / 1000 , 2) . ' km ');
            $this->assertEquals(
                $asserts['nlaps'],
                count($activity->getLaps())
            );
            $this->assertEquals(
                $asserts['durationSeconds'],
                $activity->getDurationSeconds()
            );
            $this->assertEquals(
                $asserts['distanceMeters'],
                $activity->getDistanceMeters()
            );
        }
        // $obj = json_encode($activity, JSON_PRETTY_PRINT);
        // print_r($obj);
    }

    public function testImportFileFIT()
    {
        $timeStart = microtime(true);
        $filePath = $this->base_dir . 'sample_file.fit';
        $activities = ImportFile::readFromFile($filePath);
        $timeEnd = microtime(true);

        $this->responseAnalysis(
            $activities,
            $filePath,
            $timeStart,
            $timeEnd,
            [
                'nlaps'           => 6,
                'durationSeconds' => 2250,
                'distanceMeters'  => 5070
            ]
        );
    }

    public function testImportFileGPX()
    {
        $timeStart = microtime(true);
        $filePath = $this->base_dir . 'segundo_ejemplo.gpx';
        $activities = ImportFile::readFromFile($filePath);
        $timeEnd = microtime(true);

        $this->responseAnalysis(
            $activities,
            $filePath,
            $timeStart,
            $timeEnd,
            [
                'nlaps'           => 1,
                'durationSeconds' => 7194,
                'distanceMeters'  => 52945
            ]
        );
    }

    public function testImportFileTCX()
    {
        $timeStart = microtime(true);
        $filePath = $this->base_dir . 'bici_estatica_tcx.tcx';
        $activities = ImportFile::readFromFile($filePath);
        $timeEnd = microtime(true);

        $this->responseAnalysis(
            $activities,
            $filePath,
            $timeStart,
            $timeEnd,
            [
                'nlaps'           => 1,
                'durationSeconds' => 3602,
                'distanceMeters'  => 32440
            ]
        );
    }
}
