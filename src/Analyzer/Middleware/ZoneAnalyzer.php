<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Calculate\Calculate;
use PhpSports\Analyzer\Analysis\Zone;
use PhpSports\Analyzer\Analysis\ZoneAnalysis;
use PhpSports\Model\Activity;
use \Closure;

class ZoneAnalyzer implements AnalyzerMiddlewareInterface
{
    private $matrix;
    private $zonesHR;
    private $zonesPOWER;

    public function __construct(
        array $zonesHR = [],
        array $zonesPOWER = []
    ) {
        $this->zonesHR = array_merge(
            [
                'A0' => [30, 65],
                'A1' => [65, 75],
                'A2' => [75, 85],
                'UA' => [85, 90],
                'A3' => [90, 95],
                'A4' => [95]
            ],
            $zonesHR
        );

        $this->zonesPOWER = array_merge(
            [
                'A0' => [30, 65],
                'A1' => [65, 75],
                'A2' => [75, 85],
                'UA' => [85, 90],
                'A3' => [90, 95],
                'A4' => [95]
            ],
            $zonesPOWER
        );
    }

    private function createParameterMatrix()
    {
        $this->matrix = [
            'zonesHR'    => [],
            'zonesPOWER' => []
        ];

        $default = [
            'minPercent'      => 0,
            'maxPercent'      => 0,
            'durationSeconds' => 0,
            'sumSpeed'        => 0,
            'sumPower'        => 0,
            'countSpeed'      => 0,
            'countPower'      => 0
        ];

        foreach ($this->zonesHR as $zoneName => $values) {
            $matrixZone = &$this->matrix['zonesHR'][$zoneName];
            $matrixZone = $default;
            if (isset($values[0])) {
                $matrixZone['minPercent'] = $values[0];
            }
            if (isset($values[1])) {
                $matrixZone['maxPercent'] = $values[1];
            }
        }
        foreach ($this->zonesPOWER as $zoneName => $values) {
            $matrixZone = &$this->matrix['zonesPOWER'][$zoneName];
            $matrixZone = $default;
            if (isset($values[0])) {
                $matrixZone['minPercent'] = $values[0];
            }
            if (isset($values[1])) {
                $matrixZone['maxPercent'] = $values[1];
            }
        }
    }

    private function zoneName(string $zoneCollectionName, float $percent = null)
    {
        if (isset($this->matrix[$zoneCollectionName]) && $percent) {
            foreach ($this->matrix[$zoneCollectionName] as $key => $value) {
                if (isset($value['maxPercent'])) {
                    if (($percent > $value['minPercent']) && ($percent <= $value['maxPercent'])) {
                        return $key;
                    }
                } else {
                    if ($percent > $value['minPercent']) {
                        return $key;
                    }
                }
            }
        }
        return null;
    }

    public function analyze(Activity $activity, Closure $next)
    {
        $points  = $activity->getPoints();
        if (!count($points)) {
            return $next($activity);
        }
        
        $this->createParameterMatrix();
        if ($athleteStatus = $activity->getAthleteStatus()) {
            $hrBPM      = $athleteStatus->getMaxHrBPM();
            $powerWatts = $athleteStatus->getFtpPowerWatts();

            if (!$hrBPM) {
                unset($this->matrix['zonesHR']);
            }
            if (!$powerWatts) {
                unset($this->matrix['zonesPOWER']);
            }

            $lastPoint = null;
            foreach ($points as $point) {
                $durationSeconds = Calculate::calculateDurationSeconds($lastPoint, $point);
                $speed           = $point->getSpeedMetersPerSecond();
                $power           = $point->getPowerWatts();
                if ($hrBPM) {
                    if ($hrPercent = ($point->getHrBPM() * 100) / $hrBPM) {
                        if ($zoneName = $this->zoneName('zonesHR', $hrPercent)) {
                            $matrixZone = &$this->matrix['zonesHR'][$zoneName];
                            $matrixZone['durationSeconds'] += $durationSeconds;
                            if ($speed) {
                                $matrixZone['sumSpeed'] += $speed;
                                $matrixZone['countSpeed']++;
                            }
                            if ($power) {
                                $matrixZone['sumPower'] = $power;
                                $matrixZone['countPower']++;
                            }
                        }
                    }
                }
                if ($powerWatts) {
                    if ($powerPercent = ($point->getPowerWatts() * 100) / $powerWatts) {
                        if ($zoneName = $this->zoneName('zonesPOWER', $powerPercent)) {
                            $matrixZone = &$this->matrix['zonesPOWER'][$zoneName];
                            $matrixZone['durationSeconds'] += $durationSeconds;
                            if ($speed) {
                                $matrixZone['sumSpeed'] += $speed;
                                $matrixZone['countSpeed']++;
                            }
                            if ($power) {
                                $matrixZone['sumPower'] = $power;
                                $matrixZone['countPower']++;
                            }
                        }
                    }
                }
                $lastPoint = $point;
            }

            foreach ($this->matrix as $parameter => $zones) {
                $numEmpty = 0;
                $analysis = new ZoneAnalysis($parameter);
                foreach ($zones as $zoneName => $values) {
                    $avgSpeedMetersPerSecond = $values['countSpeed'] ? ($values['sumSpeed'] / $values['countSpeed']) : null;
                    $avgPowerWatts           = $values['countPower'] ? ($values['sumPower'] / $values['countPower']) : null;

                    $zone = new Zone(
                        $zoneName,
                        $values['minPercent'],
                        $values['maxPercent'],
                        $values['durationSeconds'],
                        $avgPowerWatts,
                        $avgSpeedMetersPerSecond
                    );
                    $analysis->addZone($zone);
                    if ($values['durationSeconds'] == 0) {
                        $numEmpty++;
                    }
                }
                if ($numEmpty !== count($zones)) {
                    $activity->addAnalysis($analysis);
                }
            }
        }

        return $next($activity);
    }
}
