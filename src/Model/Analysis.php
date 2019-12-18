<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;
use PhpSports\Model\Type;
use \JsonSerializable;
use \DateTime;

class Analysis implements JsonSerializable
{
    private $parameter;
    private $valueMin;
    private $valueMax;
    private $valueAvg;
    private $valueTotal;
    private $npoints;

    public function __construct($parameter = null)
    {
        $this->parameter  = $parameter;
        $this->valueMin   = null;
        $this->valueMax   = null;
        $this->valueAvg   = null;
        $this->valueTotal = null;
        $this->npoints    = 0;
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter = null) : Analysis
    {
        $this->parameter = $parameter;
        return $this;
    }

    public function getMin()
    {
        return $this->valueMin;
    }

    public function setMin(float $valueMin = null) : Analysis
    {
        $this->valueMin = $valueMin;
        return $this;
    }

    public function getMax()
    {
        return $this->valueMax;
    }

    public function setMax(float $valueMax = null) : Analysis
    {
        $this->valueMax = $valueMax;
        return $this;
    }

    public function getAvg()
    {
        return $this->valueAvg;
    }

    public function setAvg(float $valueAvg = null) : Analysis
    {
        $this->valueAvg = $valueAvg;
        return $this;
    }

    public function getTotal()
    {
        return $this->valueTotal;
    }

    public function setTotal(float $valueTotal = null) : Analysis
    {
        $this->valueTotal = $valueTotal;
        return $this;
    }

    public function addPoint(Point $point) : Analysis
    {
        if ($value = $point->getParameter($this->parameter)) {
            $this->valueMin = (is_null($this->valueMin)) ? $value : $this->valueMin;
            $this->valueMin = ($value > 0) ? min($this->valueMin, $value) : $this->valueMin;
            $this->valueMax = (is_null($this->valueMax)) ? $value : $this->valueMax;
            $this->valueMax = ($value > 0) ? max($this->valueMax, $value) : $this->valueMax;
            $this->valueTotal = (is_null($this->valueTotal)) ? 0 : $this->valueTotal;
            $this->valueTotal += ($value > 0) ? $value : 0;
            $this->npoints++;
            $this->valueAvg = ($this->valueTotal / $this->npoints);
        }
        return $this;
    }

    public function jsonSerialize() {
        return [
            $this->parameter => [
                'min'   => $this->valueMin,
                'avg'   => $this->valueAvg,
                'max'   => $this->valueMax,
                'total' => $this->valueTotal
            ]
        ];
    }
}
