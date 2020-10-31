<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Calculate\Calculate;
use PhpSports\Analyzer\Analysis\ResumeAnalysis;
use PhpSports\Model\Activity;
use PhpSports\Model\PointCollection;
use \Closure;

class ResumeAnalyzer implements AnalyzerMiddlewareInterface
{
    private function calculatePointsActivity(PointCollection $points)
    {
        $distanceMeters      = 0;
        $durationSeconds     = 0;
        $elevationGainMeters = 0;
        $totalPoints         = 0;
        $queuePowerWatts     = [];
        $queueHrBPM          = [];
        $queueSpeed          = [];

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
            if ($speedMetersPerSecond = $point->getSpeedMetersPerSecond()) {
                $queueSpeed[] = $speedMetersPerSecond;
            }
            if ($powerWatts = $point->getPowerWatts()) {
                $queuePowerWatts[] = $powerWatts;
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
            'distanceMeters'       => $distanceMeters,
            'durationSeconds'      => $durationSeconds,
            'elevationGainMeters'  => $elevationGainMeters,
            'maxHrBPM'             => ($numHrBPM = count($queueHrBPM)) ? max($queueHrBPM) : null,
            'avgHrBPM'             => ($numHrBPM) ? (array_sum($queueHrBPM) / $numHrBPM) : null,
            'maxPowerWatts'        => ($numPowerWatts = count($queuePowerWatts)) ? max($queuePowerWatts) : null,
            'avgPowerWatts'        => ($numPowerWatts) ? (array_sum($queuePowerWatts) / $numPowerWatts) : null,
            'speedMetersPerSecond' => ($numSpeed = count($queueSpeed)) ? (array_sum($queueSpeed) / $numSpeed) : null,
            'totalPoints'          => $totalPoints
        ];
    }

    private function calculatePointsLap(PointCollection $points)
    {
        $distanceMeters      = 0;
        $durationSeconds     = 0;
        $elevationGainMeters = 0;
        $totalPoints         = 0;
        $queueSpeed          = [];

        $lastPoint = null;
        foreach ($points as $point) {
            $distance  = Calculate::calculateDistanceMeters($lastPoint, $point);
            $duration  = Calculate::calculateDurationSeconds($lastPoint, $point);
            $elevation = Calculate::calculateElevationGainMeters($lastPoint, $point);

            $distanceMeters      += $distance;
            $durationSeconds     += $duration;
            $elevationGainMeters += $elevation;

            if ($speedMetersPerSecond = $point->getSpeedMetersPerSecond()) {
                $queueSpeed[] = $speedMetersPerSecond;
            }

            $lastPoint = $point;
            $totalPoints++;
        }

        return [
            'distanceMeters'       => $distanceMeters,
            'durationSeconds'      => $durationSeconds,
            'elevationGainMeters'  => $elevationGainMeters,
            'speedMetersPerSecond' => ($numSpeed = count($queueSpeed)) ? (array_sum($queueSpeed) / $numSpeed) : null,
            'totalPoints'          => $totalPoints
        ];
    }

    public function analyze(Activity $activity, Closure $next)
    {
        $points  = $activity->getPoints();
        $calculate = $this->calculatePointsActivity($points);

        if ($athleteStatus = $activity->getAthleteStatus()) {
            if ($athleteStatus->getWeightKg()) {
                $calculate['caloriesKcal'] = Calculate::calculateKcal(
                    $athleteStatus->getWeightKg(),
                    $calculate['durationSeconds']
                );
            }

            if ($calculate['avgPowerWatts'] && ($athleteStatus->getFtpPowerWatts() > 0)) {
                $calculate['tss'] = Calculate::calculateTssFromFTP(
                    $calculate['durationSeconds'],
                    $calculate['avgPowerWatts'],
                    $athleteStatus
                );
            } elseif ($calculate['avgHrBPM'] && ($athleteStatus->getMaxHrBPM() > 0)) {
                $calculate['tss'] = Calculate::calculateTssFromHR(
                    $calculate['durationSeconds'],
                    $calculate['avgHrBPM'],
                    $athleteStatus
                );
            } else {
                $calculate['tss'] = Calculate::calculateTssFromDuration(
                    $calculate['durationSeconds']
                );
            }
        }

        unset($calculate['maxHrBPM'],
              $calculate['avgHrBPM'],
              $calculate['maxPowerWatts'],
              $calculate['avgPowerWatts']);

        $analysis = new ResumeAnalysis($calculate);
        $activity->addAnalysis($analysis);

        if ((!$activity->getStartedAt()) && count($points)) {
            reset($points);
            $time  = new \DateTime();
            $time->setTimestamp(key($points));
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
