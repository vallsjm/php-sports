<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;
use PhpSports\Analyzer\Middleware\BasicAnalyzer;
use PhpSports\Analyzer\Analyzer;

abstract class BaseParse
{
    protected $analizer;

    public function __construct()
    {
        $this->analizer = new Analyzer([
            new BasicAnalyzer()
        ]);
    }

    public function getAnalizer() : Analyzer
    {
        return $this->analizer;
    }
}
