<?php

namespace PhpSports\Model;

use PhpSports\Model\Analysis;
use PhpSports\Model\Point;
use \ArrayAccess;
use \JsonSerializable;

class AnalysisCollection extends \ArrayObject implements \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Analysis) {
            throw new \Exception('value must be an instance of Analysis');
        }

        parent::offsetSet($offset, $value);
    }

    public function addAnalysis(Analysis $analysis) : AnalysisCollection
    {
        parent::append($analysis);
        return $this;
    }

    public function analyze(Point $point) : AnalysisCollection
    {
        foreach ($this as $i) {
            $i->addPoint($point);
        }
        return $this;
    }

    public function jsonSerialize()
    {
        return (array) $this;
    }
}
