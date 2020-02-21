<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Model\Activity;
use \Closure;

class Interval2Analyzer implements AnalyzerMiddlewareInterface {

    public function analize(Activity $activity, Closure $next)
    {
        $activity->setTitle('cosita bonitaaaa');

        $timeIntervals = [5, 10, 20];
        $parameters = ['durationSeconds'];
        $timeStart = 0;
        $timeEnd = 100;
        for ($i=$timeStart;$i<=$timeEnd;$i++) {
        	$points = $activity->getPoints();
        	foreach ($timeIntervals as $timeInterval) {
        		$points = $points->filterByTimestamp($i, $i+$timeInterval);
        		foreach ($parameters as $parameter) {
	        		$values = array_map(function ($point) use ($parameter) {
	        			return $point->getParameter($parameter);	
	        		}, $points);
	        		//filtrar los nulos
	        		$avgWindow = array_sum($value) / count($values);
        		}
        	}
        }
        
        return $next($activity);
    }

}
