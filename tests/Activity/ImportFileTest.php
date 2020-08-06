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

        $this->assertActivities($activities, [
            'analysis' => [
                'resume' => [
                    'durationSeconds' => 3638,
                    'distanceMeters' => 24730,
                    'speedMetersPerSecond' => 6.80,
                    'caloriesKcal' => 711
                ]
            ]
        ]);

        // $this->renderActivities($filePath, $activities);
    }

    public function testImportFile01()
    {
        $filePath = $this->base_dir . '/source/' . 'SERIES_GOIKO.fit';
        $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);

        $this->assertActivities($activities, [
            'analysis' => [
                'resume' => [
                    'durationSeconds' => 7210,
                    'distanceMeters' => 76790,
                    'speedMetersPerSecond' => 10.65,
                    'caloriesKcal' => 2274
                ]
            ]
        ]);

        // $this->renderActivities($filePath, $activities);
    }

    public function testImportFile02()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_indoor_02.fit';
        $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);

        $this->assertActivities($activities, [
            'analysis' => [
                'resume' => [
                    'durationSeconds' => 3460,
                    'distanceMeters' => 27850,
                    'speedMetersPerSecond' => 8.32,
                    'caloriesKcal' => 326
                ]
            ]
        ]);

        // $this->renderActivities($filePath, $activities);
    }

    public function testImportFile03()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_03.fit';
        $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);

        $this->assertActivities($activities, [
            'analysis' => [
                'resume' => [
                    'durationSeconds' => 1844,
                    'distanceMeters' => 16740,
                    'elevationGainMeters' => 116.2,
                    'speedMetersPerSecond' => 9.12,
                    'caloriesKcal' => 387.24
                ]
            ]
        ]);

        // $this->renderActivities($filePath, $activities);
    }

    public function testImportFile04()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_04.gpx';
        $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);

        $this->assertActivities($activities, [
            'analysis' => [
                'resume' => [
                    'durationSeconds' => 6326,
                    'distanceMeters' => 47971.42,
                    'speedMetersPerSecond' => 7.67,
                    'caloriesKcal' => 1328.46
                ]
            ]
        ]);

        //$this->renderActivities($filePath, $activities);
    }

    public function testImportFile05()
    {
        $filePath = $this->base_dir . '/source/' . 'cycling_mountain_05.gpx';
        $activities = ImportFile::readFromFile($filePath, $this->athleteStatus);

        $this->assertActivities($activities, [
            'analysis' => [
                'resume' => [
                    'durationSeconds' => 7194,
                    'distanceMeters' => 52948.05,
                    'speedMetersPerSecond' => 7.43,
                    'caloriesKcal' => 1510.74
                ]
            ]
        ]);

        // $this->renderActivities($filePath, $activities);
    }

    public function testImportFile06()
    {
        $data = [
            "structure" => [
                "time",
                "lat",
                "lng",
                "elevation",
                "hr",
                "cadence"
            ],
            "laps" => [
                [
                    [1594910581, 41.4333, 2.1910, 34, 84, 345],
                    [1594910585, 41.4327, 2.1974, 30, 154, 445],
                    [1594910588, 41.4333, 2.1994, 16, 64, 545]
                ],
                [
                    [1594910596, 41.4286, 2.2207, 10, 54, 145],
                    [1594910597, 41.4268, 2.2345, 14, 28, 245],
                    [1594910598, 41.4319, 2.2400, 64, 79, 345]
                ]
            ]
        ];

        $filePath = '';
        $activities = ImportFile::readFromBinary('KNH', json_encode($data), $this->athleteStatus);

        $this->assertActivities($activities, [
            'analysis' => [
                'resume' => [
                    'durationSeconds' => 17,
                    'distanceMeters' => 4465.45
                ]
            ]
        ]);

        // $this->renderActivities($filePath, $activities);
    }

}
