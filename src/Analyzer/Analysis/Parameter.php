<?php

namespace PhpSports\Analyzer\Analysis;

use \JsonSerializable;

class Parameter implements JsonSerializable
{
    private $parameter;
    private $minValue;
    private $avgValue;
    private $maxValue;

    public function __construct(
        string $parameter = null,
        float $minValue = null,
        float $avgValue = null,
        float $maxValue = null
    ) {
        $this->parameter = $parameter;
        $this->minValue  = $minValue;
        $this->avgValue  = $avgValue;
        $this->maxValue  = $maxValue;
    }

    public function getParameter() : string
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter = null)
    {
        $this->parameter = $parameter;
    }

    public function getMinValue() : float
    {
        return $this->minValue;
    }

    public function setMinValue(float $minValue = null)
    {
        $this->minValue = $minValue;
    }

    public function getAvgValue() : float
    {
        return $this->avgValue;
    }

    public function setAvgValue(float $avgValue = null)
    {
        $this->avgValue = $avgValue;
    }

    public function getMaxValue() : float
    {
        return $this->maxValue;
    }

    public function setMaxValue(float $maxValue = null)
    {
        $this->maxValue = $maxValue;
    }

    public function jsonSerialize() {
        return [
            'parameter' => $this->parameter,
            'minValue'  => $this->minValue,
            'avgValue'  => $this->avgValue,
            'maxValue'  => $this->maxValue
        ];
    }
}
