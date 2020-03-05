<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Calculate\Calculate;
use PhpSports\Analyzer\Analysis\ResumeAnalysis;
use PhpSports\Model\Athlete;
use PhpSports\Model\Activity;
use PhpSports\Model\PointCollection;
use \Closure;

class ResumeAnalyzer implements AnalyzerMiddlewareInterface {

    private function calculatePointsActivity(PointCollection $points)
    {
        $distanceMeters      = 0;
        $durationSeconds     = 0;
        $elevationGainMeters = 0;
        $totalPoints         = 0;
        $queueHrBPM          = [];

        $lastPoint = null;
        foreach ($points as $point) {
            $distance  = Calculate::calculateDistanceMeters($lastPoint, $point);
            $duration  = Calculate::calculateDurationSeconds($lastPoint, $point);
            $elevation = Calculate::calculateElevationGainMeters($lastPoint, $point);

            if (is_null($point->getDistanceMeters())) {
                $point->setDistanceMeters($distanceMeters);
            }
            if (is_null($point->getSpeedMetersPerSecond()) && $duration) {
                $point->setSpeedMetersPerSecond($distance / $duration);
            }
            if ($hrBPM = $point->getHrBPM()) {
                $queueHrBPM[] = $hrBPM;
            }

            $distanceMeters      += $distance;
            $durationSeconds     += $duration;
            $elevationGainMeters += $elevation;

            $lastPoint = $point;
            $totalPoints++;
        }

        return [
            'distanceMeters'      => $distanceMeters,
            'durationSeconds'     => $durationSeconds,
            'elevationGainMeters' => $elevationGainMeters,
            'maxHrBPM'            => ($numHrBPM = count($queueHrBPM)) ? max($queueHrBPM) : null,
            'avgHrBPM'            => ($numHrBPM) ? (array_sum($queueHrBPM) / $numHrBPM) : null,
            'totalPoints'         => $totalPoints
        ];
    }

    private function calculatePointsLap(PointCollection $points)
    {
        $distanceMeters      = 0;
        $durationSeconds     = 0;
        $elevationGainMeters = 0;
        $totalPoints         = 0;

        $lastPoint = null;
        foreach ($points as $point) {
            $distance  = Calculate::calculateDistanceMeters($lastPoint, $point);
            $duration  = Calculate::calculateDurationSeconds($lastPoint, $point);
            $elevation = Calculate::calculateElevationGainMeters($lastPoint, $point);

            $distanceMeters      += $distance;
            $durationSeconds     += $duration;
            $elevationGainMeters += $elevation;

            $lastPoint = $point;
            $totalPoints++;
        }

        return [
            'distanceMeters'      => $distanceMeters,
            'durationSeconds'     => $durationSeconds,
            'elevationGainMeters' => $elevationGainMeters,
            'totalPoints'         => $totalPoints
        ];
    }

    public function analyze(Activity $activity, Closure $next)
    {
        $points  = $activity->getPoints();
        $calculate = $this->calculatePointsActivity($points);

        if ($athlete = $activity->getAthlete()) {
            if ($athlete->getWeightKg()) {
                $calculate['caloriesKcal'] = Calculate::calculateKcal(
                    $athlete->getWeightKg(),
                    $calculate['durationSeconds']
                );
            }

            if ($calculate['maxHrBPM']) {
                $calculate['tss'] = Calculate::calculateTss(
                    $calculate['durationSeconds'],
                    max($calculate['maxHrBPM'], $athlete->getMaxHrBPM()),
                    $calculate['avgHrBPM']
                );
            } else {
                $calculate['tss'] = Calculate::calculateTssFromLevel(
                    $calculate['durationSeconds']
                );
            }
        }

        unset($calculate['maxHrBPM'], $calculate['avgHrBPM']);
        $analysis = new ResumeAnalysis($calculate);
        $activity->addAnalysis($analysis);

        if (!$activity->setStartedAt()) {
            reset($points);
            $time  = new \DateTime();
            if ($activity->getTimestampOffset()) {
                $time->setTimestamp(key($points) + $activity->getTimestampOffset());
            } else {
                $time->setTimestamp(key($points));
            }
            $activity->setStartedAt($time);
        }

        $laps   = $activity->getLaps();
        foreach ($laps as $lap) {
            $filtered = $points->filterByLap($lap);
            $calculate = $this->calculatePointsLap($filtered);

            $analysis = new ResumeAnalysis($calculate);
            $lap->addAnalysis($analysis);
        }

        return $next($activity);
    }
}
