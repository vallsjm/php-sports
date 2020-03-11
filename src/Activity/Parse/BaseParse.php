<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Activity;
use PhpSports\Model\AthleteStatus;
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
    protected $athleteStatus;

    public function __construct(
        AthleteStatus $athleteStatus = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    )
    {
        $this->athleteStatus = $athleteStatus;

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

    public function setAthleteStatus(AthleteStatus $athleteStatus)
    {
        $this->athleteStatus = $athleteStatus;
    }

    public function getAthleteStatus()
    {
        return $this->athleteStatus;
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
        if ($this->athleteStatus) {
            $activity->setAthleteStatus($this->athleteStatus);
        }
        return $this->analyzer->analyze($activity);
    }

    abstract public function getFormat();
    abstract public function getType();

    public static function createInstance(
        AthleteStatus $athleteStatus = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    )
    {
        return new static($athleteStatus, $options);
    }
}
