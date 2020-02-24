<?php

namespace PhpSports\Model;

use \JsonSerializable;

class AnalysisZoneUA extends Analysis implements JsonSerializable
{
    public function getAnalyzerName() : string
    {
        return 'zoneUA';
    }

    public function merge(Analysis $anlysis) : Analysis
    {
        return $this;
    }
}
