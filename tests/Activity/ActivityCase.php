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

    private function assertArray(
        array $values,
        array $data
    )
    {
        $debug = function ($message) {
            return "MESSAGE: {$message} " . PHP_EOL ;
        };

        foreach ($data as $key => $value) {
            if (isset($values[$key])) {
                if (is_array($value)) {
                    $this->assertArray($values[$key], $value);
                } else {
                    $this->assertEquals(
                        $values[$key],
                        $value,
                        $debug("{$key}: {$values[$key]} vs {$value} analysis doesn't match"),
                        0.5
                    );
                }
            } else {
                $this->assertTrue(
                    false,
                    $debug("{$key} doesn't found")
                );
            }
        }
    }

    protected function assertActivities(
        ActivityCollection $activities,
        array $data
    )
    {
        foreach ($activities as $activity) {
            $values = json_decode(json_encode($activity), true);
            unset($values['points']);
            // print_r($values);
            $this->assertArray($values, $data);
        }
    }

    protected function renderActivities(
        string $fileName,
        ActivityCollection $activities
    )
    {
        // $json = json_encode($activities, JSON_PRETTY_PRINT);
        // print_r($json);

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
                            } elseif ($key == 'speedMetersPerSecond') {
                                $this->consoleLog(str_pad($key, 20, ' ') . ': ' . $value . ' m/s. (' . round($value * 3.6, 2) . ' Km/h)');
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
                $this->consoleLog($lap->getName() . ' ' . date('Y-m-d H:i:s', $lap->getTimestampFrom()) . ' duration: ' . gmdate("H:i:s", $analysisResume['durationSeconds']) . ' distance: ' . str_pad($analysisResume['distanceMeters'] . 'm (' . round($analysisResume['distanceMeters'] / 1000, 2) . ' km)', 20, ' ') . ' speed: '.  str_pad(round($analysisResume['speedMetersPerSecond'], 2) . 'm/s (' . round($analysisResume['speedMetersPerSecond'] * 3.6, 2). ' km/h)', 20, ' '). ' points: ' . $analysisResume['totalPoints']);
            }
        }
    }

}
