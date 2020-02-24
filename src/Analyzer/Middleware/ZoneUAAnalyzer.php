<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Calculate\Calculate;
use PhpSports\Model\AnalysisZoneUA;
use PhpSports\Model\Activity;
use PhpSports\Model\PointCollection;
use \Closure;

class ZoneUAAnalyzer implements AnalyzerMiddlewareInterface {
    private $minHrIntervalPercent;
    private $maxHrIntervalPercent;
    private $minPowerIntervalPercent;
    private $maxPowerIntervalPercent;

    public function __construct(
        $minHrIntervalPercent    = 85,
        $maxHrIntervalPercent    = 90,
        $minPowerIntervalPercent = 90,
        $maxPowerIntervalPercent = 105
    )
    {
        $this->minHrIntervalPercent    = $minHrIntervalPercent;
        $this->maxHrIntervalPercent    = $maxHrIntervalPercent;
        $this->minPowerIntervalPercent = $minPowerIntervalPercent;
        $this->maxPowerIntervalPercent = $maxPowerIntervalPercent;
    }

    // Anlize distance or speed for each point
    public function analize(Activity $activity, Closure $next)
    {
        $athlete = $activity->getAthlete();
        $points  = $activity->getPoints();

        $minHrIntervalPercent    = $this->minHrIntervalPercent;
        $maxHrIntervalPercent    = $this->maxHrIntervalPercent;
        $minPowerIntervalPercent = $this->minPowerIntervalPercent;
        $maxPowerIntervalPercent = $this->maxPowerIntervalPercent;
        $hrBPM                   = $athlete->getHrBPM();
        $powerWatts              = $athlete->getPowerWatts();

        $filtered = $points->filter(function ($point) use (
            $hrBPM,
            $powerWatts,
            $minHrIntervalPercent,
            $maxHrIntervalPercent,
            $minPowerIntervalPercent,
            $maxPowerIntervalPercent
        ) {
            $hrPercent    = $hrBPM ? (($point->getHrBPM() * 100) / $hrBPM) : null;
            $powerPercent = $powerWatts ? (($point->getPowerWatts() * 100) / $powerWatts) : null;

            if ($hrPercent) {
                if ($powerPercent) {
                    return (($hrPercent >= $minHrIntervalPercent) && ($hrPercent <= $maxHrIntervalPercent) &&
                    ($powerPercent >= $minPowerIntervalPercent) && ($powerPercent <= $maxPowerIntervalPercent));
                }
                return ($hrPercent >= $minHrIntervalPercent) && ($hrPercent <= $maxHrIntervalPercent);
            }
            return false;
        });

        $listSpeed       = [];
        $listPower       = [];
        $durationSeconds = 0;
        $lastPoint       = null;
        foreach ($filtered as $point) {
            if ($speed = $point->getSpeedMetersPerSecond()) {
                $listSpeed[] = $speed;
            }
            if ($power = $point->getPowerWatts()) {
                $listPower[] = $power;
            }
            $durationSeconds  += Calculate::calculateDurationSeconds($lastPoint, $point);
            $lastPoint = $point;
        }
        $avgSpeedMetersPerSecond = count($listSpeed) ? (array_sum($listSpeed) / count($listSpeed)) : null;
        $avgPowerWatts           = count($listPower) ? (array_sum($listPower) / count($listPower)) : null;

        $analysis = new AnalysisZoneUA([
            'avgPowerWatts' => $avgPowerWatts,
            'avgSpeedMetersPerSecond' => $avgSpeedMetersPerSecond,
            'durationSeconds' => $durationSeconds,
        ]);
        $activity->addAnalysis($analysis);

        return $next($activity);
    }

}
