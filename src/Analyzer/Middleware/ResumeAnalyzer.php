<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Calculate\Calculate;
use PhpSports\Model\AnalysisResume;
use PhpSports\Model\Athlete;
use PhpSports\Model\Activity;
use PhpSports\Model\PointCollection;
use \Closure;

class ResumeAnalyzer implements AnalyzerMiddlewareInterface {
    private $athlete;

    public function __construct(
        Athlete $athlete = null
    )
    {
        $this->athlete = $athlete;
    }

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

        $analysis = new AnalysisResume($calulate);
        $activity->addAnalysis($analysis);

        $laps   = $activity->getLaps();
        foreach ($laps as $lap) {
            $filtered = $points->filterByLap($lap);
            $calulate = $this->calculatePoints($filtered);

            $analysis = new AnalysisResume($calulate);
            $lap->addAnalysis($analysis);
        }

        if ($this->athlete) {
            $activity->setAthlete($this->athlete);
        }

        return $next($activity);
    }
}
