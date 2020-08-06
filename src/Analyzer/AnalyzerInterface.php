<?php

namespace PhpSports\Analyzer;

interface AnalyzerInterface
{
    const ANALYZER_RESUME    = 0b0001;
    const ANALYZER_PARAMETER = 0b0010;
    const ANALYZER_ZONE      = 0b0100;
    const ANALYZER_INTERVAL  = 0b1000;
}
