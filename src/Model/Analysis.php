<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;
use PhpSports\Model\Type;
use \JsonSerializable;
use \DateTime;

class Analysis implements JsonSerializable
{
    const MINVALUE = -9999999999;
    const MAXVALUE = 9999999999;

    private $parameter;
    private $valueMin;
    private $valueMax;
    private $valueAvg;
    private $valueTotal;
    private $numValues;

    public function __construct($parameter = null, $valueTotal = null)
    {
        $this->parameter  = $parameter;
        $this->valueMin   = null;
        $this->valueMax   = null;
        $this->valueAvg   = null;
        $this->valueTotal = $valueTotal;
        $this->numValues    = 1;
    }

    private function setValue(float $value = null)
    {
        if (!$value) return null;
        $value = ($value > self::MAXVALUE) ? self::MAXVALUE : $value;
        $value = ($value < self::MINVALUE) ? self::MINVALUE : $value;
        return $value;
    }

    private function getValue(float $value = null)
    {
        if (!$value) return null;
        $value = ($value == self::MAXVALUE) ? null : $value;
        $value = ($value == self::MINVALUE) ? null : $value;
        return $value;
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
        return $this->getValue($this->valueMin);
    }

    public function setMin(float $valueMin = null) : Analysis
    {
        $this->valueMin = $this->setValue($valueMin);
        return $this;
    }

    public function getMax()
    {
        return $this->getValue($this->valueMax);
    }

    public function setMax(float $valueMax = null) : Analysis
    {
        $this->valueMax = $this->setValue($valueMax);
        return $this;
    }

    public function getAvg()
    {
        return $this->getValue($this->valueAvg);
    }

    public function setAvg(float $valueAvg = null) : Analysis
    {
        $this->valueAvg = $this->setValue($valueAvg);
        return $this;
    }

    public function getTotal()
    {
        return $this->getValue($this->valueTotal);
    }

    public function setTotal(float $valueTotal = null) : Analysis
    {
        $this->valueTotal = $this->setValue($valueTotal);
        return $this;
    }

    public function getNumValues()
    {
        return $this->numValues;
    }

    public function setNumValues(int $numValues) : Analysis
    {
        $this->numValues = $numValues;
        return $this;
    }

    protected function calculateMin(float $value = null) : Analysis
    {
        $this->setMin( (is_null($this->valueMin)) ? $value : min($this->valueMin, $value) );
        return $this;
    }

    protected function calculateMax(float $value = null) : Analysis
    {
        $this->setMax( (is_null($this->valueMax)) ? $value : max($this->valueMax, $value) );
        return $this;
    }

    protected function calculateTotal(float $value = null) : Analysis
    {
        $this->setTotal( (is_null($this->valueTotal)) ? $value : ($this->valueTotal + $value) );
        $this->setAvg( (!$this->numValues) ? $this->valueTotal : ($this->valueTotal / $this->numValues) );
        return $this;
    }

    public function addPoint(Point $point) : Analysis
    {
        if ($value = $point->getParameter($this->parameter)) {
            $this->calculateMin($value);
            $this->calculateMax($value);
            $this->calculateTotal($value);
            $this->numValues++;
        }
        return $this;
    }

    public function merge(Analysis $analysis) : Analysis
    {
        $this->numValues += ($analysis->getNumValues() -2);
        $this->calculateMin($analysis->getMin());
        $this->calculateMax($analysis->getMax());
        $this->calculateTotal($analysis->getTotal());
        $this->numValues++;
        return $this;
    }

    public function jsonSerialize() {
        return [
            $this->getMin(),
            $this->getAvg(),
            $this->getMax(),
            $this->getTotal()
        ];
    }
}
