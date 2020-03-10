<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Activity;
use PhpSports\Model\Athlete;
use PhpSports\Model\ActivityCollection;
use PhpSports\Analyzer\Middleware\ResumeAnalyzer;
use PhpSports\Analyzer\Middleware\IntervalAnalyzer;
use PhpSports\Analyzer\Middleware\ZoneAnalyzer;
use PhpSports\Analyzer\Middleware\ParameterAnalyzer;
use PhpSports\Analyzer\AnalyzerInterface;
use PhpSports\Analyzer\Analyzer;

abstract class BaseParse implements AnalyzerInterface
{
    protected $analyzer;
    protected $athlete;

    public function __construct(
        Athlete $athlete = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    )
    {
        $this->athlete = $athlete;

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

        $this->analyzer = new Analyzer($middleware);
    }

    public function setAthlete(Athlete $athlete)
    {
        $this->athlete = $athlete;
    }

    public function getAthlete()
    {
        return $this->athlete;
    }

    public function getAnalyzer() : Analyzer
    {
        return $this->analyzer;
    }

    public function setAnalyzer(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function analyze(Activity $activity) : Activity
    {
        if ($this->athlete) {
            $activity->setAthlete($this->athlete);
        }
        return $this->analyzer->analyze($activity);
    }

    abstract public function getFormat();
    abstract public function getType();

    public static function createInstance(
        Athlete $athlete = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    )
    {
        return new static($athlete, $options);
    }
}
