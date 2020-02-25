<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Athlete;
use PhpSports\Model\ActivityCollection;
use PhpSports\Analyzer\Middleware\ResumeAnalyzer;
use PhpSports\Analyzer\Middleware\IntervalAnalyzer;
use PhpSports\Analyzer\Middleware\ZoneAnalyzer;
use PhpSports\Analyzer\Middleware\ParameterAnalyzer;
use PhpSports\Analyzer\Analyzer;

abstract class BaseParse
{
    CONST ANALYZER_RESUME    = 0b0001;
    CONST ANALYZER_PARAMETER = 0b0010;
    CONST ANALYZER_ZONE      = 0b0100;
    CONST ANALYZER_INTERVAL  = 0b1000;

    protected $analizer;
    protected $athlete;

    public function __construct(
        Athlete $athlete = null,
        $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    )
    {
        $this->athlete = $athlte;

        $middleware = [];
        if (self::ANALYZER_RESUME & $options) {
            $middleware[] = new ResumeAnalyzer();
        }
        if (self::ANALYZER_PARAMETER & $options) {
            $middleware[] = new ParameterAnalyzer();
        }
        if (self::ANALYZER_ZONE & $options) {
            $middleware[] = new ZoneAnalyzer();
        }
        if (self::ANALYZER_INTERVAL & $options) {
            $middleware[] = new IntervalAnalyzer();
        }

        $this->analizer = new Analyzer($middleware);
    }

    public function setAthlete(Athlete $athlete)
    {
        $this->athlete = $athlete;
    }

    public function getAthlete()
    {
        return $this->athlete;
    }

    public function getAnalizer() : Analyzer
    {
        return $this->analizer;
    }
}
