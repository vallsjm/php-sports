<?php

namespace PhpSports\Analyzer\Analysis;

use \JsonSerializable;

class Interval implements JsonSerializable
{
    private $parameter;
    private $timeIntervalSeconds;
    private $minAvg;
    private $maxAvg;

    public function __construct(
        string $parameter = null,
        int $timeIntervalSeconds = null,
        float $minAvg = null,
        float $maxAvg = null
    ) {
        $this->parameter           = $parameter;
        $this->timeIntervalSeconds = $timeIntervalSeconds;
        $this->minAvg              = $minAvg;
        $this->maxAvg              = $maxAvg;
    }

    public function getParameter() : string
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter = null)
    {
        $this->parameter = $parameter;
    }

    public function getTimeIntervalSeconds() : int
    {
        return $this->timeIntervalSeconds;
    }

    public function setTimeIntervalSeconds(int $timeIntervalSeconds = null)
    {
        $this->timeIntervalSeconds = $timeIntervalSeconds;
    }

    public function getMinAvg() : float
    {
        return $this->minAvg;
    }

    public function setMinAvg(float $minAvg = null)
    {
        $this->minAvg = $minAvg;
    }

    public function getMaxAvg() : float
    {
        return $this->maxAvg;
    }

    public function setMaxAvg(float $maxAvg = null)
    {
        $this->maxAvg = $maxAvg;
    }

    public function jsonSerialize() {
        return [
            'parameter'           => $this->parameter,
            'timeIntervalSeconds' => $this->timeIntervalSeconds,
            'minAvg'              => $this->minAvg,
            'maxAvg'              => $this->maxAvg
        ];
    }
}
