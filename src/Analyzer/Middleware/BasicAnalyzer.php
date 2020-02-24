<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Calculate\Calculate;
use PhpSports\Model\Activity;
use PhpSports\Model\PointCollection;
use \Closure;

class BasicAnalyzer implements AnalyzerMiddlewareInterface {

    private function calculatePoints(PointCollection $points)
    {
        $distanceMeters      = 0;
        $durationSeconds     = 0;
        $elevationGainMeters = 0;

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
        }

        return [
            'distanceMeters'      => $distanceMeters,
            'durationSeconds'     => $durationSeconds,
            'elevationGainMeters' => $elevationGainMeters
        ];
    }

    // Anlize distance or speed for each point
    public function analize(Activity $activity, Closure $next)
    {
        $points = $activity->getPoints();
        $calulate = $this->calculatePoints($points);

        $laps   = $activity->getLaps();
        foreach ($laps as $lap) {
            $filtered = $points->filterByLap($lap);
            $calulate = $this->calculatePoints($filtered);
            $lap->setDistanceMeters($calulate['distanceMeters']);
            $lap->setDurationSeconds($calulate['durationSeconds']);
            $lap->setElevationGainMeters($calulate['elevationGainMeters']);
        }

        return $next($activity);
    }

}
