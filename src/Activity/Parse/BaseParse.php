<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\Athlete;
use PhpSports\Model\ActivityCollection;
use PhpSports\Analyzer\Middleware\ResumeAnalyzer;
use PhpSports\Analyzer\Middleware\IntervalAnalyzer;
use PhpSports\Analyzer\Middleware\ZoneUAAnalyzer;
use PhpSports\Analyzer\Analyzer;

abstract class BaseParse
{
    protected $analizer;

    public function __construct()
    {
        $athlete = new Athlete();
        $athlete->setHrBPM(120);
        // $athlete->setPowerWatts(100);

        $this->analizer = new Analyzer([
            new ResumeAnalyzer($athlete),
            new IntervalAnalyzer(
                [5, 60, 300, 1200, 3600],
                ['altitudeMeters','elevationMeters','speedMetersPerSecond','hrBPM','cadenceRPM','powerWatts']
            ),
            new ZoneUAAnalyzer(
                85,
                90,
                90,
                105
            )
        ]);
    }

    public function getAnalizer() : Analyzer
    {
        return $this->analizer;
    }
}
