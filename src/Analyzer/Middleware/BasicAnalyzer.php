<?php

namespace PhpSports\Analyzer\Middleware;

use PhpSports\Analyzer\AnalyzerMiddlewareInterface;
use PhpSports\Model\Activity;
use \Closure;

class BasicAnalyzer implements AnalyzerMiddlewareInterface {

    public function analize(Activity $activity, Closure $next)
    {
        $activity->setTitle('cosita bonitaaaa');
        return $next($activity);
    }

}
