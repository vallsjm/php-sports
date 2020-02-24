<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Calculate\Calculate;
use PhpSports\Model\Interval;
use PhpSports\Model\AnalysisInterval;
use PhpSports\Model\Activity;
use PhpSports\Model\PointCollection;
use \Closure;

class IntervalAnalyzer implements AnalyzerMiddlewareInterface {
    private $timeIntervals;
    private $parameters;
    private $matrix;

    public function __construct(
        array $timeIntervals = [5, 60, 300, 1200, 3600],
        array $parameters    = ['altitudeMeters','elevationMeters','speedMetersPerSecond','hrBPM','cadenceRPM','powerWatts']
    )
    {
        $this->timeIntervals = $timeIntervals;
        $this->parameters    = $parameters;
    }

    private function createParameterMatrix()
    {
        $this->matrix = [];
        foreach ($this->parameters as $parameter) {
            $this->matrix[$parameter] = [];
            foreach ($this->timeIntervals as $timeInterval) {
                $this->matrix[$parameter][$timeInterval] = [
                    'max'       => -999999999,
                    'min'       => +999999999,
                    'window'    => [],
                    'pending'   => []
                ];
            }
        }
    }

    private function loadParameterMatrix(
        PointCollection $points,
        int $timeStart,
        int $timeEnd
    )
    {
        $this->createParameterMatrix();
        $defaultValues = array_fill($timeStart, $timeEnd - $timeStart, null);
        foreach ($this->parameters as $parameter) {
            $values = $defaultValues;
            foreach ($points as $point) {
                if ($value = $point->getParameter($parameter)) {
                    $pos = $point->getTimestamp();
                    $values[$pos] = $value;
                }
            }
            if (count(array_filter($values))) {
                foreach ($this->timeIntervals as $timeInterval) {
                    $window         = array_slice($values, 0, $timeInterval);
                    $windowFiltered = array_filter($window);
                    $this->matrix[$parameter][$timeInterval]['window']  = $window;
                    $this->matrix[$parameter][$timeInterval]['pending'] = array_slice($values, $timeInterval, $timeEnd - $timeStart);
                    if ($numValues = count($windowFiltered)) {
                        $avgValue = array_sum($windowFiltered) / $numValues;
                        $this->matrix[$parameter][$timeInterval]['max'] = $avgValue;
                        $this->matrix[$parameter][$timeInterval]['min'] = $avgValue;
                    }
                }
            } else { // podamos pq no hay valores de ese parametro
                unset($this->matrix[$parameter]);
            }
        }
        $this->parameters = array_keys($this->matrix); // borramos los parametros podados
    }

    private function shiftParameterMatrix()
    {
        foreach ($this->parameters as $parameter) {
            foreach ($this->timeIntervals as $timeInterval) {
                if (count($this->matrix[$parameter][$timeInterval]['pending'])) {
                    $item = array_shift($this->matrix[$parameter][$timeInterval]['pending']);
                    array_push($this->matrix[$parameter][$timeInterval]['window'], $item);
                    array_shift($this->matrix[$parameter][$timeInterval]['window']);
                }
            }
        }
    }

    // Anlize distance or speed for each point
    public function analize(Activity $activity, Closure $next)
    {
        $points = $activity->getPoints();
        reset($points);
        $timeStart = key($points);
        end($points);
        $timeEnd  = key($points);

        $this->loadParameterMatrix(
            $points,
            $timeStart,
            $timeEnd
        );

        $limitParameters = count($this->parameters) * count($this->timeIntervals);
        for ($i=$timeStart;$i<=$timeEnd;$i++) {
            $this->shiftParameterMatrix();
            $doneParameters = 0;
            foreach ($this->parameters as $parameter) {
                foreach ($this->timeIntervals as $timeInterval) {
                    if (count($this->matrix[$parameter][$timeInterval]['pending'])) {
                        $windowFiltered = array_filter($this->matrix[$parameter][$timeInterval]['window']);
                        if ($numValues = count($windowFiltered)) {
                            $avgValue  = array_sum($windowFiltered) / $numValues;
                            $this->matrix[$parameter][$timeInterval]['max'] = max($avgValue, $this->matrix[$parameter][$timeInterval]['max']);
                            $this->matrix[$parameter][$timeInterval]['min'] = min($avgValue, $this->matrix[$parameter][$timeInterval]['min']);
                        }
                    } else {
                        $doneParameters++;
                    }
                }
            }
            // podamos en caso de haber terminado todos los parametros
            if ($doneParameters >= $limitParameters) {
                break;
            }
        }

        $intervals = [];
        foreach ($this->parameters as $parameter) {
            foreach ($this->timeIntervals as $timeInterval) {
                $interval = new Interval(
                    $parameter,
                    $timeInterval,
                    $this->matrix[$parameter][$timeInterval]['min'],
                    $this->matrix[$parameter][$timeInterval]['max']
                );
                $intervals[] = $interval;
            }
        }

        $analysis = new AnalysisInterval($intervals);
        $activity->addAnalysis($analysis);

        return $next($activity);
    }

}
