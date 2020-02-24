<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Calculate\Calculate;
use PhpSports\Model\Activity;
use PhpSports\Model\PointCollection;
use \Closure;

class IntervalAnalyzer implements AnalyzerMiddlewareInterface {

    private $intervals;

    public function __construct(array $timeIntervals = [5, 60, 300, 1200, 3600])
    {
        $this->intervals = $this->createIntervals($intervals);
    }

    private function createIntervals(array $timeIntervals = [])
    {
        $this->intervals = [];
        foreach ($timeIntervals as $timeInterval) {
            $this->intervals[$timeInterval] = [
                'max'       => 0,
                'min'       => 0,
                'timeLimit' => 0,
                'queue'     => []
            ];
        }
    }

    // Anlize distance or speed for each point
    public function analize(Activity $activity, Closure $next)
    {
        $points = $activity->getPoints();
        $timeStart = 0;
        $timeEnd   = 100;
        // foreach ($this->intervals as $timeInterval => $data) {
        //     $data['timeLimit'] = $timeEnd - $timeInterval;
        // }
        // for ($i=$timeStart;$i<=$timeEnd;$i++) {
        //     $pointsWindow = $points->filterByTime();
        // }
        //
        // foreach ($points as $point) {
        //     $timeCurrent = $point->getTimestamp();
        //
        // }

        return $next($activity);
    }

}
