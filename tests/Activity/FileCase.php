<?php
namespace Tests\Activity;

use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ExportFile;
use PhpSports\Activity\ImportFile;
use PhpSports\Model\ActivityCollection;

class FileCase extends TestCase
{
    protected $base_dir;
    protected $testFiles;

    public function setUp()
    {
        $this->base_dir = __DIR__ . '/../../samples';

        $this->testFiles = [
            [
                'fileName' => 'cycling_indoor_01.fit',
                'generate' => [
                    'cycling_indoor_01.knh',
                    'cycling_indoor_01.tcx'
                ],
                'asserts'  => [
                    [
                        'id' => null,
                        'sport' => null,
                        'name' => null,
                        'startedAt' => '2017-11-14 17:32:59',
                        'resume' => [
                            'distanceMeters'  => 24730,
                            'durationSeconds' => 3638,
                            'numLaps' => 3,
                            'numPoints' => 1817
                        ],
                        'analysis' => [
                            ['parameter' => 'hrBPM', 'intervalTimeSeconds' => 0, 'min' => 93, 'avg' => 142.33736929004, 'max' => 169, 'total' => 258627],
                            ['parameter' => 'cadenceRPM', 'intervalTimeSeconds' => 0, 'min' => 3, 'avg' => 83.701997780244, 'max' => 96, 'total' => 150831],
                            ['parameter' => 'powerWatts', 'intervalTimeSeconds' => 0, 'min' => 6, 'avg' => 180.52019922524, 'max' => 275, 'total' => 326200]
                        ]
                    ]
                ]
            ],
            [
                'fileName' => 'cycling_indoor_02.fit',
                'generate' => [
                    'cycling_indoor_02.knh',
                    'cycling_indoor_02.tcx'
                ],
                'asserts'  => [
                    [
                        'id' => null,
                        'sport' => null,
                        'name' => null,
                        'startedAt' => '2017-11-23 05:15:34',
                        'resume' => [
                            'distanceMeters'  => 27850,
                            'durationSeconds' => 3460,
                            'numLaps' => 1,
                            'numPoints' => 488
                        ],
                        'analysis' => [
                            ['parameter' => 'hrBPM', 'intervalTimeSeconds' => 0, 'min' => 72, 'avg' => 107.53668763103, 'max' => 139, 'total' => 51295],
                            ['parameter' => 'cadenceRPM', 'intervalTimeSeconds' => 0, 'min' => 36, 'avg' => 82.413721413721, 'max' => 113, 'total' => 39641],
                            ['parameter' => 'altitudeMeters', 'intervalTimeSeconds' => 0, 'min' => 656.6, 'avg' => 658.53483606557, 'max' => 661.4, 'total' => 321365]
                        ]
                    ]
                ]
            ],
            [
                'fileName' => 'cycling_mountain_03.fit',
                'generate' => [
                    'cycling_mountain_03.knh',
                    'cycling_mountain_03.tcx',
                    'cycling_mountain_03.gpx'
                ],
                'asserts'  => [
                    [
                        'id' => null,
                        'sport' => null,
                        'name' => null,
                        'startedAt' => '2017-11-07 17:25:44',
                        'resume' => [
                            'distanceMeters'  => 14573,
                            'durationSeconds' => 1844,
                            'numLaps' => 2,
                            'numPoints' => 920
                        ],
                        'analysis' => [
                            ['parameter' => 'hrBPM', 'intervalTimeSeconds' => 0, 'min' => 90, 'avg' => 128.58260869565, 'max' => 150, 'total' => 118296],
                            ['parameter' => 'altitudeMeters', 'intervalTimeSeconds' => 0, 'min' => 1407.4, 'avg' => 1434.4739130435, 'max' => 1514.2, 'total' => 1319716]
                        ]
                    ]
                ]
            ],
            [
                'fileName' => 'cycling_mountain_04.gpx',
                'generate' => [
                    'cycling_mountain_04.knh',
                    'cycling_mountain_04.tcx',
                    'cycling_mountain_04.gpx'
                ],
                'asserts'  => [
                    [
                        'id' => null,
                        'sport' => null,
                        'name' => 'Ejercicio 28.10.2017',
                        'startedAt' => '2017-10-28 12:54:11',
                        'resume' => [
                            'distanceMeters'  => 47971,
                            'durationSeconds' => 6326,
                            'numLaps' => 1,
                            'numPoints' => 6259
                        ],
                        'analysis' => [
                            ['parameter' => 'hrBPM', 'intervalTimeSeconds' => 0, 'min' => 95, 'avg' => 123.03387122544, 'max' => 153, 'total' => 770069],
                            ['parameter' => 'elevationMeters', 'intervalTimeSeconds' => 0, 'min' => 568.400024, 'avg' => 585.7010062208, 'max' => 612.200012, 'total' => 3665902.597936]
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function consoleLog($text)
    {
        fwrite(STDERR, $text . PHP_EOL);
    }

    protected function generateAnalysisAsserts(
        $fileName,
        ActivityCollection $activities,
        array $testFile
    )
    {
        $debug = function ($message, $fileName, $nactivity) {
            return "MESSAGE: {$message} " . PHP_EOL . "FILE: {$fileName}" . PHP_EOL . "ACTIVITY: {$nactivity}" . PHP_EOL . PHP_EOL;
        };

        $nactivity = 0;
        foreach ($activities as $activity) {
            if (isset($testFile['asserts'][$nactivity]['id'])) {
                $this->assertEquals(
                    $testFile['asserts'][$nactivity]['id'],
                    $activity->getId(),
                    $debug('Id field doesn\'t match', $fileName, $nactivity)
                );
            }
            if (isset($testFile['asserts'][$nactivity]['sport'])) {
                $this->assertEquals(
                    $testFile['asserts'][$nactivity]['sport'],
                    $activity->getSport(),
                    $debug('Sport field doesn\'t match', $fileName, $nactivity)
                );
            }
            if (isset($testFile['asserts'][$nactivity]['name'])) {
                $this->assertEquals(
                    $testFile['asserts'][$nactivity]['name'],
                    $activity->getName(),
                    $debug('Name field doesn\'t match', $fileName, $nactivity)
                );
            }
            if (isset($testFile['asserts'][$nactivity]['startedAt'])) {
                $this->assertEquals(
                    $testFile['asserts'][$nactivity]['startedAt'],
                    $activity->getStartedAt()->format('Y-m-d H:i:s'),
                    $debug('StartedAt field doesn\'t match', $fileName, $nactivity)
                );
            }
            if (isset($testFile['asserts'][$nactivity]['resume']['durationSeconds'])) {
                $this->assertEquals(
                    $testFile['asserts'][$nactivity]['resume']['durationSeconds'],
                    round($activity->getDurationSeconds()),
                    $debug('DurationSeconds field doesn\'t match', $fileName, $nactivity),
                    1
                );
            }
            if (isset($testFile['asserts'][$nactivity]['resume']['distanceMeters'])) {
                $this->assertEquals(
                    $testFile['asserts'][$nactivity]['resume']['distanceMeters'],
                    round($activity->getDistanceMeters()),
                    $debug('DistanceMeters field doesn\'t match', $fileName, $nactivity)
                );
            }
            if (isset($testFile['asserts'][$nactivity]['resume']['numLaps'])) {
                $this->assertEquals(
                    $testFile['asserts'][$nactivity]['resume']['numLaps'],
                    count($activity->getLaps()),
                    $debug('numLaps field doesn\'t match', $fileName, $nactivity)
                );
            }
            if (isset($testFile['asserts'][$nactivity]['resume']['numPoints'])) {
                $this->assertEquals(
                    $testFile['asserts'][$nactivity]['resume']['numPoints'],
                    $activity->getNumPoints(),
                    $debug('numPoints field doesn\'t match', $fileName, $nactivity)
                );
            }
            foreach ($testFile['asserts'][$nactivity]['analysis'] as $values) {
                $this->assertTrue(
                    ($analysis = $activity->getAnalysisOrNull($values['parameter'], $values['intervalTimeSeconds'])) ? true : false,
                    $debug($values['parameter'] . ' analysis doesn\'t found', $fileName, $nactivity)
                );

                $this->assertEquals(
                    $values['min'],
                    $analysis->getMin(),
                    $debug('minValue of ' . $values['parameter'] . ' analysis doesn\'t match', $fileName, $nactivity)
                );
                $this->assertEquals(
                    $values['avg'],
                    $analysis->getAvg(),
                    $debug('avgValue of ' . $values['parameter'] . ' analysis doesn\'t match', $fileName, $nactivity)
                );
                $this->assertEquals(
                    $values['max'],
                    $analysis->getMax(),
                    $debug('maxValue of ' . $values['parameter'] . ' analysis doesn\'t match', $fileName, $nactivity)
                );
                $this->assertEquals(
                    $values['total'],
                    $analysis->getTotal(),
                    $debug('totalValue of ' . $values['parameter'] . ' analysis doesn\'t match', $fileName, $nactivity),
                    1
                );
            }
            $nactivity++;
        }
    }

    protected function renderActivities(
        int $duration,
        string $fileName,
        ActivityCollection $activities
    )
    {
        $this->consoleLog(PHP_EOL);
        $this->consoleLog('FILE: ' . $fileName . ', DURATION: ' . $duration . ' s.');
        foreach ($activities as $activity) {
            $this->consoleLog('ACTIVITY: ' . $activity->getName() . ', DATE: ' . $activity->getStartedAt()->format('Y-m-d H:i:s') . ', DURATION: ' . gmdate("H:i:s", $activity->getDurationSeconds()) . ', DISTANCE: ' . round($activity->getDistanceMeters() / 1000 , 2) . ' km, ELEVATION GAIN: ' . round($activity->getElevationGainMeters(), 2). 'm.'. PHP_EOL . PHP_EOL);

            foreach ($activity->getLaps() as $lap) {
                $this->consoleLog($lap->getName() . ' ' . $lap->getStartedAt()->format('Y-m-d H:i:s') . ' duration: ' . gmdate("H:i:s", $lap->getDurationSeconds()) . ' distance: ' . round($lap->getDistanceMeters() / 1000, 2) . ' km' . ' points: ' . $lap->getNumPoints());
            }

            $this->consoleLog(PHP_EOL);
            foreach ($activity->getAnalysis() as $analysis) {
                $this->consoleLog(str_pad($analysis->getParameter(), 20, ' ') . '  interval: ' . str_pad($analysis->getIntervalTimeSeconds(), 10, ' ') . '  min: ' . str_pad($analysis->getMin(), 20, ' ') . ' avg: ' . str_pad($analysis->getAvg(), 20, ' ') . ' max: ' . str_pad($analysis->getMax(), 20, ' ') . ' total: ' . str_pad($analysis->getTotal(), 20, ' '));
            }
        }
    }

}
