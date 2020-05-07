<?php

namespace PhpSports\Analyzer\Analysis;

use PhpSports\Analyzer\Analysis\Interval;
use PhpSports\Model\Analysis;
use PhpSports\Model\AnalysisInterface;
use \JsonSerializable;

class IntervalAnalysis extends Analysis implements JsonSerializable, AnalysisInterface
{
    public function __construct(
        $data = null
    ) {
        $this->data = [];
    }

    public function getName() : string
    {
        return 'intervals';
    }

    public function addInterval(Interval $interval = null)
    {
        $pos = $interval->getParameter();
        if (!isset($this->data[$pos])) {
            $this->data[$pos] = [];
        }
        $seconds = $interval->getTimeIntervalSeconds();
        $this->data[$pos][$seconds] = $interval;
    }

    public function merge(Analysis $analysis) : Analysis
    {
        return new static(array_replace_recursive($analysis->getData(), $this->data));
    }
}
