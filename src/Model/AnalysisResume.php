<?php

namespace PhpSports\Model;

use \JsonSerializable;

class AnalysisResume extends Analysis implements JsonSerializable
{
    public function getAnalyzerName() : string
    {
        return 'resume';
    }

    public function merge(Analysis $anlysis) : Analysis
    {
        return $this;
    }
}
