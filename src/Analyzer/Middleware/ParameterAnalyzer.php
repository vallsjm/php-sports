<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Analyzer\Analysis\Parameter;
use PhpSports\Analyzer\Analysis\ParameterAnalysis;
use PhpSports\Model\Activity;
use PhpSports\Model\Type;
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

    public function analyze(Activity $activity, Closure $next)
    {
        $points = $activity->getPoints();

        if (!count($points)) {
            return $next($activity);
        }

        $matrix = [];
        foreach ($this->parameters as $parameter) {
            $matrix[$parameter] = [
                'min'   => 9999999999,
                'max'   => -9999999999,
                'sum'   => 0,
                'count' => 0
            ];
        }

        foreach ($points as $point) {
            foreach ($this->parameters as $parameter) {
                if ($value = $point->getParameter($parameter)) {
                    $matrix[$parameter]['min'] = min($value, $matrix[$parameter]['min']);
                    $matrix[$parameter]['max'] = max($value, $matrix[$parameter]['max']);
                    $matrix[$parameter]['sum'] += $value;
                    $matrix[$parameter]['count'] ++;
                }
            }
        }

        $analysis = new ParameterAnalysis();
        foreach ($matrix as $parameterName => $values) {
            if ($values['count']) {
                $parameter = new Parameter(
                    $parameterName,
                    $values['min'],
                    $values['sum'] / $values['count'],
                    $values['max']
                );
                $analysis->addParameter($parameter);
            }
        }
        $activity->addAnalysis($analysis);

        return $next($activity);
    }
}
