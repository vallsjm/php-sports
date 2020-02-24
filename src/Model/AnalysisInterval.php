<?php

namespace PhpSports\Model;

use \JsonSerializable;

class AnalysisInterval extends Analysis implements JsonSerializable
{
    public function getAnalyzerName() : string
    {
        return 'interval';
    }

    public function merge(Analysis $anlysis) : Analysis
    {
        return $this;
    }
}
