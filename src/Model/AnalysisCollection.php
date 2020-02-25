<?php

namespace PhpSports\Model;

use PhpSports\Model\Analysis;

class AnalysisCollection extends \ArrayObject implements \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Analysis) {
            throw new \Exception('value must be an instance of Analysis');
        }

        parent::offsetSet($offset, $value);
    }

    public function addAnalysis(Analysis $analysis)
    {
        $pos = $analysis->getName();
        if (isset($this[$pos])) {
            $this[$pos] = $this[$pos]->merge($analysis);
        } else {
            $this[$pos] = $analysis;
        }
    }

    public function jsonSerialize()
    {
        return (array) $this;
    }
}
