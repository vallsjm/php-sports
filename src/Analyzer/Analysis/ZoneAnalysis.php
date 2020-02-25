<?php

namespace PhpSports\Analyzer\Analysis;

use PhpSports\Analyzer\Analysis\Zone;
use PhpSports\Model\Analysis;
use PhpSports\Model\AnalysisInterface;
use \JsonSerializable;

class ZoneAnalysis extends Analysis implements JsonSerializable, AnalysisInterface
{
    private $name;

    public function __construct(
        $name = null
    ) {
        $this->name = $name;
        $this->data = [];
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function addZone(Zone $zone = null)
    {
        $this->data[] = $zone;
    }

    public function merge(Analysis $analysis) : Analysis
    {
        return new static(array_merge($analysis->getData(), $this->data));
    }
}
