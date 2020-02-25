<?php

namespace PhpSports\Analyzer;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Model\Activity;
use InvalidArgumentException;
use Closure;

// http://esbenp.github.io/2015/07/31/implementing-before-after-middleware/
// https://github.com/esbenp/onion
class Analyzer {

    private $middlewares;

    public function __construct(array $middlewares = [])
    {
        $this->middlewares = $middlewares;
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

    public function analize(Activity $object)
    {
        $coreFunction = $this->createCoreFunction($object);

        $middlewares = array_reverse($this->middlewares);

        $completeObject = array_reduce($middlewares, function($nextMiddleware, $middleware){
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
        return function($object)  {
            return $object;
        };
    }

    private function createMiddleware($nextMiddleware, $middleware)
    {
        return function($object) use($nextMiddleware, $middleware) {
            //$timeStart = microtime(true);
            $ret = $middleware->analize($object, $nextMiddleware);
            //echo PHP_EOL . "object: " . get_class($middleware) . ", duration: " . (microtime(true) - $timeStart) . "s.";
            return $ret;
        };
    }

}
