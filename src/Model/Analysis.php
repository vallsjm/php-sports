<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;
use PhpSports\Model\Type;
use PhpSports\Utils\Utils;
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
    private $intervalTimeSeconds;
    private $intervalQueue;
    private $numValues;

    public function __construct(
        string $parameter = null,
        float $valueTotal = null,
        int $intervalTimeSeconds = 0
    ) {
        $this->parameter           = $parameter;
        $this->valueMin            = null;
        $this->valueMax            = null;
        $this->valueAvg            = null;
        $this->valueTotal          = $valueTotal;
        $this->numValues           = 1;
        $this->intervalTimeSeconds = $intervalTimeSeconds;
        $this->intervalQueue       = [];
    }

    private static function setValue(float $value = null)
    {
        if (!$value) return null;
        $value = ($value > self::MAXVALUE) ? self::MAXVALUE : $value;
        $value = ($value < self::MINVALUE) ? self::MINVALUE : $value;
        return $value;
    }

    private static function getValue(float $value = null)
    {
        if (!$value) return null;
        $value = ($value == self::MAXVALUE) ? null : $value;
        $value = ($value == self::MINVALUE) ? null : $value;
        return $value;
    }

    public function getIntervalTimeSeconds()
    {
        return $this->intervalTimeSeconds;
    }

    public function setIntervalTimeSeconds(int $intervalTimeSeconds = 0) : Analysis
    {
        $this->intervalTimeSeconds = $intervalTimeSeconds;
        return $this;
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
        return self::getValue($this->valueMin);
    }

    public function setMin(float $valueMin = null) : Analysis
    {
        $this->valueMin = self::setValue($valueMin);
        return $this;
    }

    public function getMax()
    {
        return self::getValue($this->valueMax);
    }

    public function setMax(float $valueMax = null) : Analysis
    {
        $this->valueMax = self::setValue($valueMax);
        return $this;
    }

    public function getAvg()
    {
        return self::getValue($this->valueAvg);
    }

    public function setAvg(float $valueAvg = null) : Analysis
    {
        $this->valueAvg = self::setValue($valueAvg);
        return $this;
    }

    public function getTotal()
    {
        return self::getValue($this->valueTotal);
    }

    public function setTotal(float $valueTotal = null) : Analysis
    {
        $this->valueTotal = self::setValue($valueTotal);
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

    public function analyzePoint(Point $point) : Analysis
    {
        if ($value = $point->getParameter($this->parameter)) {
            if ($this->intervalTimeSeconds > 0) {
                $timeEnd   = $point->getTimestamp();
                $timeLimit = $timeEnd - $this->intervalTimeSeconds;
                $this->intervalQueue[$timeEnd] = $value;

                foreach ($this->intervalQueue as $timestamp => $value) {
                    if ($timestamp >= $timeLimit) {
                        break;
                    } else {
                        unset($this->intervalQueue[$timestamp]);
                    }
                }
                $queueAvg = array_sum($this->intervalQueue)/count($this->intervalQueue);

                $this->calculateMin($queueAvg);
                $this->calculateMax($queueAvg);
                $this->calculateTotal($queueAvg);
                $this->numValues++;
            } else {
                $this->calculateMin($value);
                $this->calculateMax($value);
                $this->calculateTotal($value);
                $this->numValues++;
            }
        }
        return $this;
    }

    public function merge(Analysis $analysis) : Analysis
    {
        if (($analysis->getParameter() != $this->getParameter()) ||
            ($analysis->getIntervalTimeSeconds() != $this->getIntervalTimeSeconds())) {
                throw new \Exception('Analysis merge not valid');
        }

        if ($analysis->getNumValues() > 1) {
            $this->numValues += ($analysis->getNumValues() -2);
            $this->calculateMin($analysis->getMin());
            $this->calculateMax($analysis->getMax());
            $this->calculateTotal($analysis->getTotal());
            $this->numValues++;
        }
        return $this;
    }

    public function jsonSerialize() {
        return [
            $this->getParameter(),
            $this->getIntervalTimeSeconds(),
            $this->getMin(),
            $this->getAvg(),
            $this->getMax(),
            $this->getTotal()
        ];
    }
}
