<?php

namespace PhpSports\Analyzer;

use PhpSports\Model\Activity;
use \Closure;

interface AnalyzerMiddlewareInterface {

    public function analyze(Activity $activity, Closure $next);

}
