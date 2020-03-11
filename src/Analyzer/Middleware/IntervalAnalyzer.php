<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Analysis\Interval;
use PhpSports\Analyzer\Analysis\IntervalAnalysis;
use PhpSports\Model\Activity;
use PhpSports\Model\PointCollection;
use PhpSports\Model\Type;
use \Closure;

class IntervalAnalyzer implements AnalyzerMiddlewareInterface {
    private $timeIntervals;
    private $parameters;
    private $matrix;

    public function __construct(
        array $timeIntervals = [],
        array $parameters    = []
    )
    {
        foreach ($parameters as $key) {
            if (!in_array($key, Type::POINT)) {
                throw new \Exception('parameter "' . $key . '" is not valid analysis parameter');
            }
        }

        $this->timeIntervals = array_merge(
            [5, 60, 300, 1200, 3600],
            $timeIntervals
        );

        $this->parameters = array_merge(
            [
                'speedMetersPerSecond',
                'hrBPM',
                'cadenceRPM',
                'powerWatts'
            ],
            $parameters
        );
    }

    private function createParameterMatrix()
    {
        $this->matrix = [];
        foreach ($this->parameters as $parameter) {
            $this->matrix[$parameter] = [];
            foreach ($this->timeIntervals as $timeInterval) {
                $this->matrix[$parameter][$timeInterval] = [
                    'max'     => -999999999,
                    'min'     => +999999999,
                    'sum'     => null,
                    'count'   => null,
                    'window'  => [],
                    'pending' => []
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
                    $matrixInterval = &$this->matrix[$parameter][$timeInterval];
                    $window         = array_slice($values, 0, $timeInterval);
                    $windowFiltered = array_filter($window);
                    $matrixInterval['window']   = $window;
                    $matrixInterval['pending']  = array_slice($values, $timeInterval, $timeEnd - $timeStart);
                    if ($numValues = count($windowFiltered)) {
                        $sumValue = array_sum($windowFiltered);
                        $avgValue = $sumValue / $numValues;
                        $matrixInterval['sum']   = $sumValue;
                        $matrixInterval['count'] = $numValues;
                        $matrixInterval['max']   = $avgValue;
                        $matrixInterval['min']   = $avgValue;
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
                $matrixInterval = &$this->matrix[$parameter][$timeInterval];
                if (count($matrixInterval['pending'])) {
                    $pushItem = array_shift($matrixInterval['pending']);
                    array_push($matrixInterval['window'], $pushItem);
                    $popItem = array_shift($matrixInterval['window']);
                    if (!empty($pushItem) || !empty($popItem)) {
                        $matrixInterval['filtered'] = array_filter($matrixInterval['window']);
                    }
                }
            }
        }
    }

    public function analyze(Activity $activity, Closure $next)
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

        $matrixInterval = [];
        foreach ($this->parameters as $parameter) {
            foreach ($this->timeIntervals as $timeInterval) {
                $matrixInterval[] = &$this->matrix[$parameter][$timeInterval];
            }
        }

        for ($i=$timeStart;$i<=$timeEnd;$i++) {
            foreach ($matrixInterval as $pos => &$matrix) {
                if (count($matrix['pending'])) {
                    $pushItem = array_shift($matrix['pending']);
                    array_push($matrix['window'], $pushItem);
                    $popItem = array_shift($matrix['window']);

                    if (!empty($pushItem) || !empty($popItem)) {
                        $matrix['sum']   += ($pushItem - $popItem);
                        $matrix['count'] += !empty($pushItem) - !empty($popItem);
                        if ($matrix['count']) {
                            $avgValue  = $matrix['sum'] / $matrix['count'];
                            $matrix['max'] = max($avgValue, $matrix['max']);
                            $matrix['min'] = min($avgValue, $matrix['min']);
                        }
                    }
                } else {
                    unset($matrixInterval[$pos]);
                }
            }

            if (!count($matrixInterval)) {
                break;
            }
        }

        $analysis = new IntervalAnalysis();
        foreach ($this->parameters as $parameter) {
            foreach ($this->timeIntervals as $timeInterval) {
                $interval = new Interval(
                    $parameter,
                    $timeInterval,
                    $this->matrix[$parameter][$timeInterval]['min'],
                    $this->matrix[$parameter][$timeInterval]['max']
                );
                $analysis->addInterval($interval);
            }
        }
        $activity->addAnalysis($analysis);

        return $next($activity);
    }

}
