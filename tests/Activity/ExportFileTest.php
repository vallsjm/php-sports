<?php

use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ExportFile;
use PhpSports\Activity\ImportFile;

final class ExportFileTest extends TestCase
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
    }

    // public function testExportFileFIT()
    // {
    //     //TODO
    // }
    //
    // public function testExportFileGPX()
    // {
    //     $filePath = $this->base_dir . 'sample_file.fit';
    //     $activities = ImportFile::readFromFile($filePath);
    //
    //     $timeStart = microtime(true);
    //     $filePath = $this->base_dir . 'sample_file2.gpx';
    //     ExportFile::saveToFile($activities, $filePath, true);
    //     $timeEnd = microtime(true);
    //
    // }
    //
    // public function testExportFileTCX()
    // {
    //     $filePath = $this->base_dir . 'sample_file.fit';
    //     $activities = ImportFile::readFromFile($filePath);
    //
    //     $timeStart = microtime(true);
    //     $filePath = $this->base_dir . 'sample_file2.tcx';
    //     ExportFile::saveToFile($activities, $filePath, true);
    //     $timeEnd = microtime(true);
    // }

    public function testExportFileKNH()
    {
        $filePath = $this->base_dir . '/source/cycling_indoor_01.fit';
        $activities = ImportFile::readFromFile($filePath);

        $pretty = true;

        $filePath = $this->base_dir . '/destination/cycling_indoor_01.knh';
        ExportFile::saveToFile($activities, $filePath, $pretty);

        $filePath = $this->base_dir . '/destination/cycling_indoor_01.gpx';
        ExportFile::saveToFile($activities, $filePath, $pretty);

        $filePath = $this->base_dir . '/destination/cycling_indoor_01.tcx';
        ExportFile::saveToFile($activities, $filePath, $pretty);

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
