<?php
namespace Tests\Activity;

use Tests\Activity\ActivityCase;
use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ImportFile;
use PhpSports\Model\Athlete;
use PhpSports\Timer\Timer;

final class ImportFileTest extends ActivityCase
{
    public function testImportFile00()
    {
        $filePath = $this->base_dir . '/source/' . 'sample_file.fit';
        $athlete = $this->athlete;
        $timer = new Timer();
        $activities = $timer->addFunction('read', function () use ($filePath, $athlete) {
            return ImportFile::readFromFile($filePath, $athlete);
        });

        // echo $timer;
        // $json = json_encode($activities, JSON_PRETTY_PRINT);
        // print_r($json);
        $this->renderActivities($filePath, $activities);
    }

    public function testImportFile01()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_indoor_01.fit';
        $activities = ImportFile::readFromFile($filePath, $this->athlete);
        $this->renderActivities($filePath, $activities);
    }

    public function testImportFile02()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_indoor_02.fit';
        $activities = ImportFile::readFromFile($filePath, $this->athlete);
        $this->renderActivities($filePath, $activities);
    }

    public function testImportFile03()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_03.fit';
        $activities = ImportFile::readFromFile($filePath, $this->athlete);
        $this->renderActivities($filePath, $activities);
    }

    public function testImportFile04()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_04.gpx';
        $activities = ImportFile::readFromFile($filePath, $this->athlete);
        $this->renderActivities($filePath, $activities);
    }

    public function testImportFile05()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_05.gpx';
        $activities = ImportFile::readFromFile($filePath, $this->athlete);
        $this->renderActivities($filePath, $activities);
    }

}
