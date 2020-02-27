<?php

namespace PhpSports\Analyzer;

interface AnalyzerInterface
{
    CONST ANALYZER_RESUME    = 0b0001;
    CONST ANALYZER_PARAMETER = 0b0010;
    CONST ANALYZER_ZONE      = 0b0100;
    CONST ANALYZER_INTERVAL  = 0b1000;
}
