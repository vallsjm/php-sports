<?php

namespace PhpSports\Model;

use PhpSports\Model\Analysis;
use PhpSports\Model\Point;
use \ArrayAccess;
use \JsonSerializable;

class AnalysisCollection extends \ArrayObject implements \JsonSerializable
{
    public function __construct(
        array $default = null
    ) {
        foreach ($default as $parameter => $intervals) {
            $analysis = new Analysis($parameter, null);
            $this->addAnalysis($analysis);
            foreach ($intervals as $interval) {
                $analysis = new Analysis($parameter, null, $interval);
                $this->addAnalysis($analysis);
            }
        }
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Analysis) {
            throw new \Exception('value must be an instance of Analysis');
        }

        parent::offsetSet($offset, $value);
    }

    public function addAnalysis(Analysis $analysis) : AnalysisCollection
    {
        $pos = $analysis->getParameter() . '-' . $analysis->getIntervalTimeSeconds();
        $this[$pos] = $analysis;
        return $this;
    }

    public function getAnalysisOrCreate(string $parameter, int $intervalTimeSeconds = 0) : Analysis
    {
        $analysis = $this->getAnalysisOrNull($parameter, $intervalTimeSeconds);
        if (!$analysis) {
            $analysis = new Analysis($parameter, null, $intervalTimeSeconds);
            $this->addAnalysis($analysis);
        }
        return $analysis;
    }

    public function getAnalysisOrNull(string $parameter, int $intervalTimeSeconds = 0)
    {
        $pos = "{$parameter}-{$intervalTimeSeconds}";
        if (isset($this[$pos])) {
            return $this[$pos];
        }
        return null;
    }

    public function analyzePoint(Point $point) : AnalysisCollection
    {
        // $tocreate = array_diff_key(Point::getStructure(), array_keys((array) $this));
        // foreach ($tocreate as $key) {
        //     $analysis = new Analysis($key);
        //     $this[$key] = $analysis;
        // }

        foreach ($this as $i) {
            $i->analyzePoint($point);
        }
        return $this;
    }

    public function merge(AnalysisCollection $analysisCollection) : AnalysisCollection
    {
        foreach ($analysisCollection as $pos => $fromAnalysis) {
            if (isset($this[$pos])) {
                $this[$pos]->merge($fromAnalysis);
            } else {
                $this[$pos] = $fromAnalysis;
            }
        }
        return $this;
    }

    public function jsonSerialize()
    {
        $structure = [
            "parameter",
            "intervalTimeSeconds",
            "valueMin",
            "valueAvg",
            "valueMax",
            "valueTotal"
        ];

        return [
            "structure"  => ($this->count()) ? $structure : [],
            "parameters" => array_values((array) $this)
        ];
    }
}
