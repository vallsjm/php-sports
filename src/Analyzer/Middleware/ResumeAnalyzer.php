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

    private function calculatePoints(PointCollection $points)
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

            if (!$point->getDistanceMeters()) {
                $point->setDistanceMeters($distance);
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
            'totalPoints'         => $totalPoints
        ];
    }

    // Anlize distance or speed for each point
    public function analize(Activity $activity, Closure $next)
    {
        $points = $activity->getPoints();

        $calulate = $this->calculatePoints($points);

        $analysis = new ResumeAnalysis($calulate);
        $activity->addAnalysis($analysis);

        if (!$activity->setStartedAt()) {
            reset($points);
            $time  = new \DateTime();
            $time->setTimestamp(key($points));
            $activity->setStartedAt($time);
        }

        $laps   = $activity->getLaps();
        foreach ($laps as $lap) {
            $filtered = $points->filterByLap($lap);
            $calulate = $this->calculatePoints($filtered);

            $analysis = new ResumeAnalysis($calulate);
            $lap->addAnalysis($analysis);
        }

        return $next($activity);
    }
}
