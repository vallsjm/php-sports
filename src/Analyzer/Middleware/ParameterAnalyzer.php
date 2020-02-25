<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Calculate\Calculate;
use PhpSports\Analyzer\Analysis\Parameter;
use PhpSports\Analyzer\Analysis\ParameterAnalysis;
use PhpSports\Model\Athlete;
use PhpSports\Model\Activity;
use PhpSports\Model\PointCollection;
use \Closure;

class ParameterAnalyzer implements AnalyzerMiddlewareInterface {
    private $parameters;

    public function __construct(
        array $parameters = []
    )
    {
        foreach ($parameters as $key) {
            if (!in_array($key, Type::POINT)) {
                throw new \Exception('parameter "' . $key . '" is not valid analysis parameter');
            }
        }

        $this->parameters = array_merge(
            [
                'altitudeMeters',
                'elevationMeters',
                'speedMetersPerSecond',
                'hrBPM',
                'cadenceRPM',
                'powerWatts'
            ],
            $parameters
        );
    }

    public function analize(Activity $activity, Closure $next)
    {
        $points = $activity->getPoints();

        $matrix = [];
        foreach ($this->parameters as $parameter) {
            $matrix[$parameter] = [];
        }

        foreach ($points as $point) {
            foreach ($this->parameters as $parameter) {
                if ($value = $point->getParameter($parameter)) {
                    $matrix[$parameter][] = $value;
                }
            }
        }

        $analysis = new ParameterAnalysis();
        foreach ($matrix as $parameterName => $values) {
            if (count($values)) {
                $parameter = new Parameter(
                    $parameterName,
                    min($values),
                    array_sum($values) / count($values),
                    max($values)
                );
                $analysis->addParameter($parameter);
            }
        }
        $activity->addAnalysis($analysis);

        return $next($activity);
    }
}
