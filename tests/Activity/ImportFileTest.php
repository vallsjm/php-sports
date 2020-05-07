<?php
namespace Tests\Activity;

use Tests\Activity\ActivityCase;
use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ImportFile;
use PhpSports\Timer\Timer;

final class ImportFileTest extends ActivityCase
{
    public function testImportFile00()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_indoor_01.fit';
        $athleteStatus = $this->athleteStatus;
        $timer = new Timer();

        $import = ImportFile::createInstanceFromFile($filePath, $athleteStatus);
        $activities = $timer->addFunction('read', function () use ($filePath, $import) {
            return $import->readFromFile($filePath);
        });

        // echo $import->getAnalyzer()->getTimer();
        // echo $timer;
        // $json = json_encode($activities[0]->getAnalysis(), JSON_PRETTY_PRINT);
        // print_r($json);
        $this->renderActivities($filePath, $activities);
    }

    // public function testImportFile01()
    // {
    //     $filePath = $this->base_dir . '/source/' . 'cycling_indoor_01.fit';
    //     $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);
    //     $this->renderActivities($filePath, $activities);
    // }
    //
    // public function testImportFile02()
    // {
    //     $filePath = $this->base_dir . '/source/' . 'cycling_indoor_02.fit';
    //     $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);
    //     $this->renderActivities($filePath, $activities);
    // }
    //
    // public function testImportFile03()
    // {
    //     $filePath = $this->base_dir . '/source/' . 'cycling_mountain_03.fit';
    //     $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);
    //     $this->renderActivities($filePath, $activities);
    // }
    //
    // public function testImportFile04()
    // {
    //     $filePath = $this->base_dir . '/source/' . 'cycling_mountain_04.gpx';
    //     $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);
    //     $this->renderActivities($filePath, $activities);
    // }
    //
    // public function testImportFile05()
    // {
    //     $filePath = $this->base_dir . '/source/' . 'cycling_mountain_05.gpx';
    //     $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);
    //     $this->renderActivities($filePath, $activities);
    // }

}
