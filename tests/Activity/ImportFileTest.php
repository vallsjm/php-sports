<?php
namespace Tests\Activity;

use Tests\Activity\FileCase;
use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ImportFile;
use PhpSports\Activity\Parse\ParseFile\ParseFileFIT;
use PhpSports\Model\Athlete;

final class ImportFileTest extends FileCase
{
    public function testImportFile00()
    {
        $filePath = $this->base_dir . '/source/' . 'sample_file.fit';

        $athlete = new Athlete();
        $athlete->setMaxHrBPM(120);
        $athlete->setWeightKg(80);
        $athlete->setFtpPowerWatts(100);

        $activities = ImportFile::readFromFile($filePath, $athlete);
        $json = json_encode($activities, JSON_PRETTY_PRINT);
        //print_r($json);
    }

    public function testImportFile01()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_indoor_01.fit';

        $athlete = new Athlete();
        $athlete->setMaxHrBPM(120);
        $athlete->setWeightKg(80);
        $athlete->setFtpPowerWatts(100);

        $activities = ImportFile::readFromFile($filePath, $athlete);
        $json = json_encode($activities, JSON_PRETTY_PRINT);
        //print_r($json);
    }

    public function testImportFile02()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_indoor_02.fit';

        $athlete = new Athlete();
        $athlete->setMaxHrBPM(120);
        $athlete->setWeightKg(80);
        $athlete->setFtpPowerWatts(100);

        $activities = ImportFile::readFromFile($filePath, $athlete);
        $json = json_encode($activities, JSON_PRETTY_PRINT);
    }

    public function testImportFile03()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_03.fit';

        $athlete = new Athlete();
        $athlete->setMaxHrBPM(120);
        $athlete->setWeightKg(80);
        $athlete->setFtpPowerWatts(100);

        $activities = ImportFile::readFromFile($filePath, $athlete);
        $json = json_encode($activities, JSON_PRETTY_PRINT);
    }

    public function testImportFile04()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_04.gpx';

        $athlete = new Athlete();
        $athlete->setMaxHrBPM(120);
        $athlete->setWeightKg(80);
        $athlete->setFtpPowerWatts(100);

        $activities = ImportFile::readFromFile($filePath, $athlete);
        $json = json_encode($activities, JSON_PRETTY_PRINT);
    }

    public function testImportFile05()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_05.gpx';

        $athlete = new Athlete();
        $athlete->setMaxHrBPM(120);
        $athlete->setWeightKg(80);
        $athlete->setFtpPowerWatts(100);

        $activities = ImportFile::readFromFile($filePath, $athlete);
        $json = json_encode($activities, JSON_PRETTY_PRINT);
    }

}
