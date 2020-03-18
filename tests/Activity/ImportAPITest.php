<?php
namespace Tests\Activity;

use Tests\Activity\ActivityCase;
use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ImportAPI;

final class ImportAPITest extends ActivityCase
{
    public function testImportAPI00()
    {
        $filePath = $this->base_dir . '/source/' . 'garmin.json';
        $activities = ImportAPI::readFromFile('GARMIN', $filePath, $this->athleteStatus);
        $this->renderActivities($filePath, $activities);
    }

    public function testImportAPI01()
    {
        $filePath = $this->base_dir . '/source/' . 'strava-ride.json';
        $activities = ImportAPI::readFromFile('STRAVA', $filePath, $this->athleteStatus);
        $this->renderActivities($filePath, $activities);
    }

    public function testImportAPI02()
    {
        $filePath = $this->base_dir . '/source/' . 'strava-workout.json';
        $activities = ImportAPI::readFromFile('STRAVA', $filePath, $this->athleteStatus);
        $this->renderActivities($filePath, $activities);
    }

    public function testImportAPI03()
    {
        $filePath = $this->base_dir . '/source/' . 'strava-run.json';
        $activities = ImportAPI::readFromFile('STRAVA', $filePath, $this->athleteStatus);
        $this->renderActivities($filePath, $activities);

        // $json = json_encode($activities, JSON_PRETTY_PRINT);
        // print_r($json);

    }


    // public function testImportFile01()
    // {
    //     $filePath = $this->base_dir . '/source/' . 'strava.json';
    //     $activities = ImportAPI::readFromFile('STRAVA', $filePath, $this->athlete);
    //     $this->renderActivities($filePath, $activities);
    // }

}
