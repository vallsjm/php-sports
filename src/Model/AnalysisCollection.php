<?php

namespace PhpSports\Model;

use PhpSports\Model\Analysis;
use PhpSports\Model\Point;
use \ArrayAccess;
use \JsonSerializable;

class AnalysisCollection extends \ArrayObject implements \JsonSerializable
{
    private $parameters;
    private $structure;

    public function __construct(
        array $parameters = []
    ) {
        $this->parameters = $parameters;
        $this->structure  = [];
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
        $structure = Point::getStructure();
        if ($structure != $this->structure) {
            $tocreate        = array_intersect($structure, array_keys($this->parameters));
            $tocreate        = array_diff($tocreate, array_keys((array) $this));
            $this->structure = $structure;
            foreach ($tocreate as $parameter) {
                $analysis = new Analysis($parameter, null);
                $this->addAnalysis($analysis);
                foreach ($this->parameters[$parameter] as $interval) {
                    $analysis = new Analysis($parameter, null, $interval);
                    $this->addAnalysis($analysis);
                }
            }
        }
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
                $this->addAnalysis(clone $fromAnalysis);
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
