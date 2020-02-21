<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Model\Activity;
use \Closure;

class Interval2Analyzer implements AnalyzerMiddlewareInterface {

//  primero definimos un array de parametros con timestamp => valor, filtrado quitando nulos
//    $data[
//        'speed' =>  [10002 => 10, 100003 => 4]
//    ]

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
                if (true) { //poda
                    $points = $points->filterByTimestamp($i, $i+$timeInterval);
                    foreach ($parameters as $parameter) {
                        $values = array_map(function ($point) use ($parameter) {
                            return $point->getParameter($parameter);    
                        }, $points);
                        //filtrar los nulos
                        $avgWindow = array_sum($values) / count($values);
                    }
                }
        	}
        }
        
        return $next($activity);
    }

}
