<?php
namespace Tests\Activity;

use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ExportFile;
use PhpSports\Activity\ImportFile;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\AthleteStatus;

class ActivityCase extends TestCase
{
    protected $base_dir;
    protected $athleteStatus;

    public function setUp()
    {
        $this->base_dir = __DIR__ . '/../../samples';

        $athleteStatus = new AthleteStatus();
        $athleteStatus->setMaxHrBPM(120);
        $athleteStatus->setWeightKg(80);
        $athleteStatus->setFtpPowerWatts(100);

        $this->athleteStatus = $athleteStatus;
    }

    protected function consoleLog($text)
    {
        fwrite(STDERR, $text . PHP_EOL);
    }

    // protected function generateAnalysisAsserts(
    //     $fileName,
    //     ActivityCollection $activities,
    //     array $testFile
    // )
    // {
    //     $debug = function ($message, $fileName, $nactivity) {
    //         return "MESSAGE: {$message} " . PHP_EOL . "FILE: {$fileName}" . PHP_EOL . "ACTIVITY: {$nactivity}" . PHP_EOL . PHP_EOL;
    //     };
    //
    //     $nactivity = 0;
    //     foreach ($activities as $activity) {
    //         if (isset($testFile['asserts'][$nactivity]['id'])) {
    //             $this->assertEquals(
    //                 $testFile['asserts'][$nactivity]['id'],
    //                 $activity->getId(),
    //                 $debug('Id field doesn\'t match', $fileName, $nactivity)
    //             );
    //         }
    //         if (isset($testFile['asserts'][$nactivity]['sport'])) {
    //             $this->assertEquals(
    //                 $testFile['asserts'][$nactivity]['sport'],
    //                 $activity->getSport(),
    //                 $debug('Sport field doesn\'t match', $fileName, $nactivity)
    //             );
    //         }
    //         if (isset($testFile['asserts'][$nactivity]['name'])) {
    //             $this->assertEquals(
    //                 $testFile['asserts'][$nactivity]['name'],
    //                 $activity->getName(),
    //                 $debug('Name field doesn\'t match', $fileName, $nactivity)
    //             );
    //         }
    //         if (isset($testFile['asserts'][$nactivity]['startedAt'])) {
    //             $this->assertEquals(
    //                 $testFile['asserts'][$nactivity]['startedAt'],
    //                 $activity->getStartedAt()->format('Y-m-d H:i:s'),
    //                 $debug('StartedAt field doesn\'t match', $fileName, $nactivity)
    //             );
    //         }
    //         if (isset($testFile['asserts'][$nactivity]['resume']['durationSeconds'])) {
    //             $this->assertEquals(
    //                 $testFile['asserts'][$nactivity]['resume']['durationSeconds'],
    //                 round($activity->getDurationSeconds()),
    //                 $debug('DurationSeconds field doesn\'t match', $fileName, $nactivity),
    //                 1
    //             );
    //         }
    //         if (isset($testFile['asserts'][$nactivity]['resume']['distanceMeters'])) {
    //             $this->assertEquals(
    //                 $testFile['asserts'][$nactivity]['resume']['distanceMeters'],
    //                 round($activity->getDistanceMeters()),
    //                 $debug('DistanceMeters field doesn\'t match', $fileName, $nactivity)
    //             );
    //         }
    //         if (isset($testFile['asserts'][$nactivity]['resume']['numLaps'])) {
    //             $this->assertEquals(
    //                 $testFile['asserts'][$nactivity]['resume']['numLaps'],
    //                 count($activity->getLaps()),
    //                 $debug('numLaps field doesn\'t match', $fileName, $nactivity)
    //             );
    //         }
    //         if (isset($testFile['asserts'][$nactivity]['resume']['numPoints'])) {
    //             $this->assertEquals(
    //                 $testFile['asserts'][$nactivity]['resume']['numPoints'],
    //                 $activity->getNumPoints(),
    //                 $debug('numPoints field doesn\'t match', $fileName, $nactivity)
    //             );
    //         }
    //         foreach ($testFile['asserts'][$nactivity]['analysis'] as $values) {
    //             $this->assertTrue(
    //                 ($analysis = $activity->getAnalysisOrNull($values['parameter'], $values['intervalTimeSeconds'])) ? true : false,
    //                 $debug($values['parameter'] . ' analysis doesn\'t found', $fileName, $nactivity)
    //             );
    //
    //             $this->assertEquals(
    //                 $values['min'],
    //                 $analysis->getMin(),
    //                 $debug('minValue of ' . $values['parameter'] . ' analysis doesn\'t match', $fileName, $nactivity)
    //             );
    //             $this->assertEquals(
    //                 $values['avg'],
    //                 $analysis->getAvg(),
    //                 $debug('avgValue of ' . $values['parameter'] . ' analysis doesn\'t match', $fileName, $nactivity)
    //             );
    //             $this->assertEquals(
    //                 $values['max'],
    //                 $analysis->getMax(),
    //                 $debug('maxValue of ' . $values['parameter'] . ' analysis doesn\'t match', $fileName, $nactivity)
    //             );
    //             $this->assertEquals(
    //                 $values['total'],
    //                 $analysis->getTotal(),
    //                 $debug('totalValue of ' . $values['parameter'] . ' analysis doesn\'t match', $fileName, $nactivity),
    //                 1
    //             );
    //         }
    //         $nactivity++;
    //     }
    // }

    protected function renderActivities(
        string $fileName,
        ActivityCollection $activities
    )
    {
        $this->consoleLog(PHP_EOL);
        $this->consoleLog('FILE: ' . $fileName);
        foreach ($activities as $activity) {
            $this->consoleLog('ACTIVITY: ' . $activity->getTitle() . ', SPORT: ' . $activity->getSport() . ', DATE: ' . $activity->getStartedAt()->format('Y-m-d H:i:s'));
            foreach ($activity->getAnalysis() as $analysis) {
                switch ($analysis->getName()) {
                    case 'resume':
                        $this->consoleLog(PHP_EOL . 'resume: ' . PHP_EOL);
                        foreach ($analysis->getData() as $key => $value) {
                            if ($key == 'distanceMeters') {
                                $this->consoleLog(str_pad($key, 20, ' ') . ': ' . $value . ' m. (' . round($value /1000, 2). ' Km)');
                            } elseif ($key == 'durationSeconds') {
                                $this->consoleLog(str_pad($key, 20, ' ') . ': ' . $value . ' s. (' . gmdate("H:i:s", $value) . ')');
                            } else {
                                $this->consoleLog(str_pad($key, 20, ' ') . ': ' . $value);
                            }
                        }
                    break;
                    case 'parameters':
                        $this->consoleLog(PHP_EOL . 'parameters: ' . PHP_EOL);
                        foreach ($analysis->getData() as $key => $value) {
                            $this->consoleLog(str_pad($value->getParameter(), 20, ' ') . '  min: ' . str_pad($value->getMinValue(), 20, ' ') . ' avg: ' . str_pad($value->getAvgValue(), 20, ' ') . ' max: ' . str_pad($value->getMaxValue(), 20, ' '));
                        }
                    break;
                    case 'intervals':
                        $this->consoleLog(PHP_EOL . 'intervals: ' . PHP_EOL);
                        foreach ($analysis->getData() as $parameter => $values) {
                            foreach ($values as $interval => $value) {
                                $this->consoleLog(str_pad($value->getParameter(), 20, ' ') . '  interval: ' . str_pad('(' . gmdate("H:i:s", $value->getTimeIntervalSeconds()) . ') ' . $value->getTimeIntervalSeconds() . 's.', 20, ' ')  . ' min: ' . str_pad($value->getMinAvg(), 20, ' ') . ' max: ' . str_pad($value->getMaxAvg(), 20, ' '));
                            }
                        }
                    break;
                    case 'zonesHR':
                        $this->consoleLog(PHP_EOL . 'zonesHR: ' . PHP_EOL);
                        foreach ($analysis->getData() as $value) {
                            $this->consoleLog(str_pad($value->getName(), 8, ' ') . '  percent: ' . str_pad($value->getMinPercent() . '% - ' .$value->getMaxPercent() . '%', 12, ' ')  . ' duration: ' . str_pad(gmdate("H:i:s", $value->getDurationSeconds()), 12, ' ') . ' avgFTP: ' . str_pad($value->getAvgPowerWatts(), 20, ' ') . ' avgSPEED: ' . $value->getAvgSpeedMetersPerSecond() . ' m/s (' . round($value->getAvgSpeedKilometersPerHour(), 2) . ' Km/h)');
                        }
                    break;
                    case 'zonesPOWER':
                        $this->consoleLog(PHP_EOL . 'zonesHR: ' . PHP_EOL);
                        foreach ($analysis->getData() as $value) {
                            $this->consoleLog(str_pad($value->getName(), 8, ' ') . '  percent: ' . str_pad($value->getMinPercent() . '% - ' .$value->getMaxPercent() . '%', 12, ' ')  . ' duration: ' . str_pad(gmdate("H:i:s", $value->getDurationSeconds()), 12, ' ') . ' avgFTP: ' . str_pad($value->getAvgPowerWatts(), 20, ' ') . ' avgSPEED: ' . $value->getAvgSpeedMetersPerSecond() . ' m/s (' . round($value->getAvgSpeedKilometersPerHour(), 2) . ' Km/h)');
                        }
                    break;
                }
            }

            $this->consoleLog(PHP_EOL . 'laps: ' . PHP_EOL);
            foreach ($activity->getLaps() as $lap) {
                $analysisResume = $lap->getAnalysis()->filterByName('resume')->getData();
                $this->consoleLog($lap->getName() . ' ' . date('Y-m-d H:i:s', $lap->getTimestampFrom()) . ' duration: ' . gmdate("H:i:s", $analysisResume['durationSeconds']) . ' distance: ' . str_pad($analysisResume['distanceMeters'] . 'm (' . round($analysisResume['distanceMeters'] / 1000, 2) . ' km)', 20, ' ') . ' points: ' . $analysisResume['totalPoints']);
            }
        }
    }

}
