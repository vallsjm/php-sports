<?php
namespace Tests\Activity;

use Tests\Activity\FileCase;
use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ImportFile;
use PhpSports\Activity\Parse\ParseFile\ParseFileFIT;
use PhpSports\Model\Athlete;

final class ImportFileTest extends FileCase
{

    public function testImportFiles()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_04.gpx';

        $athlete = new Athlete();
        $athlete->setMaxHrBPM(120);
        $athlete->setWeightKg(80);
        $athlete->setFtpPowerWatts(100);

        $activities = ImportFile::readFromFile($filePath, $athlete);

        // $parseFIT = new ParseFileFIT($athlete, ParseFileFIT::ANALYZER_RESUME);
        // $parseFIT = new ParseFileFIT($athlete, ParseFileFIT::ANALYZER_RESUME);
        // $parseFIT->setAthlete($athlete);
        //$activities = $parseFIT->readFromFile($filePath);

        $json = json_encode($activities, JSON_PRETTY_PRINT);
        print_r($json);

        // foreach ($this->testFiles as $testFile) {
        //     // if ($testFile['fileName'] == 'cycling_indoor_01.fit') {
        //         $timeStart = microtime(true);
        //         $filePath = $this->base_dir . '/source/' . $testFile['fileName'];
        //         $activities = ImportFile::readFromFile($filePath);
        //         $duration = microtime(true) - $timeStart;
        //
        //         $this->renderActivities(
        //             $duration,
        //             $filePath,
        //             $activities
        //         );
        //
        //         // $this->generateAnalysisAsserts(
        //         //     $filePath,
        //         //     $activities,
        //         //     $testFile
        //         // );
        //     // }
        // }
    }
}
