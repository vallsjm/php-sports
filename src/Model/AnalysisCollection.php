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
        $pos = $analysis->getParameter();
        $this[$pos] = $analysis;
        return $this;
    }

    public function getAnalysisOrCreate(string $parameter) : Analysis
    {
        if (isset($this[$parameter])) {
            return $this[$parameter];
        }
        $analysis = new Analysis($parameter);
        $this->addAnalysis($analysis);
        return $analysis;
    }

    public function getAnalysisOrNull(string $parameter)
    {
        if (isset($this[$parameter])) {
            return $this[$parameter];
        }
        return null;
    }

    public function analyze(Point $point) : AnalysisCollection
    {
        $tocreate = array_diff_key(Point::getStructure(), array_keys((array) $this));
        foreach ($tocreate as $key) {
            $analysis = new Analysis($key);
            $this[$key] = $analysis;
        }
        foreach ($this as $i) {
            $i->addPoint($point);
        }
        return $this;
    }

    public function merge(AnalysisCollection $analysisCollection) : AnalysisCollection
    {
        foreach ($analysisCollection as $parameter => $fromAnalysis) {
            $toAnalysis = $this->getAnalysisOrCreate($parameter);
            $toAnalysis->merge($fromAnalysis);
        }
        return $this;
    }

    public function jsonSerialize()
    {
        $structure = [
            "valueMin",
            "valueAvg",
            "valueMax",
            "valueTotal"
        ];

        return [
            "structure" => ($this->count()) ? $structure : [],
            "parameters" => (array) $this
        ];
    }
}
