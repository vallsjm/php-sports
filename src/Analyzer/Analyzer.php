<?php

namespace PhpSports\Analyzer;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Model\Activity;
use PhpSports\Timer\Timer;
use InvalidArgumentException;
use Closure;

class Analyzer
{
    private $middlewares;
    private $timer;

    public function __construct(array $middlewares = [], Timer $timer = null)
    {
        $this->middlewares = $middlewares;
        $this->timer       = $timer;
    }

    public function setTimer(Timer $timer = null)
    {
        $this->timer = $timer;
    }

    public function getTimer() : Timer
    {
        return $this->timer;
    }

    public function addMiddleware($middlewares)
    {
        if ($middlewares instanceof Analyzer) {
            $middlewares = $middlewares->toArray();
        }

        if ($middlewares instanceof AnalyzerMiddlewareInterface) {
            $middlewares = [$middlewares];
        }

        if (!is_array($middlewares)) {
            throw new InvalidArgumentException(get_class($middlewares) . " is not a valid middleware.");
        }

        return new static(array_merge($this->middlewares, $middlewares));
    }

    public function analyze(Activity $object)
    {
        $coreFunction = $this->createCoreFunction($object);

        $middlewares = array_reverse($this->middlewares);

        $completeObject = array_reduce($middlewares, function ($nextMiddleware, $middleware) {
            return $this->createMiddleware($nextMiddleware, $middleware);
        }, $coreFunction);

        return $completeObject($object);
    }

    public function toArray()
    {
        return $this->middlewares;
    }

    private function createCoreFunction(Activity $object)
    {
        return function ($object) {
            return $object;
        };
    }

    private function createMiddleware($nextMiddleware, $middleware)
    {
        if ($this->timer) {
            $timer = $this->timer;
            return function ($object) use ($nextMiddleware, $middleware, $timer) {
                $timeStart = microtime(true);
                $className = get_class($middleware);
                return $middleware->analyze($object, function ($object) use ($timeStart, $nextMiddleware, $className, $timer) {
                    $timer->setFunctionDuration($className, microtime(true) - $timeStart);
                    return $nextMiddleware($object);
                });
            };
        }

        return function ($object) use ($nextMiddleware, $middleware) {
            return $middleware->analyze($object, $nextMiddleware);
        };
    }
}
