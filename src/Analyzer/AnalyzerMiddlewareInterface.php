<?php

namespace PhpSports\Analyzer;

use PhpSports\Model\Activity;
use \Closure;

interface AnalyzerMiddlewareInterface {

    public function analize(Activity $activity, Closure $next);
}
