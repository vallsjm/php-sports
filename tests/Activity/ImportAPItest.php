<?php
namespace Tests\Activity;

use Tests\Activity\ActivityCase;
use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ImportAPI;
use PhpSports\Model\Athlete;

final class ImportAPITest extends ActivityCase
{
    public function testImportAPI00()
    {
        $filePath = $this->base_dir . '/source/' . 'garmin.json';
        $activities = ImportAPI::readFromFile('GARMIN', $filePath, $this->athlete);
        $this->renderActivities($filePath, $activities);
    }

    // public function testImportFile01()
    // {
    //     $filePath = $this->base_dir . '/source/' . 'strava.json';
    //     $activities = ImportAPI::readFromFile('STRAVA', $filePath, $this->athlete);
    //     $this->renderActivities($filePath, $activities);
    // }

}
