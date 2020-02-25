<?php

namespace PhpSports\Analyzer\Analysis;

use \JsonSerializable;

class Zone implements JsonSerializable
{
    private $name;
    private $minPercent;
    private $maxPercent;
    private $durationSeconds;
    private $avgPowerWatts;
    private $avgSpeedMetersPerSecond;

    public function __construct(
        string $name = null,
        int $minPercent = null,
        int $maxPercent = null,
        int $durationSeconds = null,
        float $avgPowerWatts = null,
        float $avgSpeedMetersPerSecond = null
    ) {
        $this->name                    = $name;
        $this->minPercent              = $minPercent;
        $this->maxPercent              = $maxPercent;
        $this->durationSeconds         = $durationSeconds;
        $this->avgPowerWatts           = $avgPowerWatts;
        $this->avgSpeedMetersPerSecond = $avgSpeedMetersPerSecond;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name = null)
    {
        $this->name = $name;
    }

    public function getMinPercent() : int
    {
        return $this->minPercent;
    }

    public function setMinPercent(int $minPercent = null)
    {
        $this->minPercent = $minPercent;
    }

    public function getMaxPercent() : int
    {
        return $this->maxPercent;
    }

    public function setMaxPercent(int $maxPercent = null)
    {
        $this->maxPercent = $maxPercent;
    }

    public function getDurationSeconds() : int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(int $durationSeconds = null)
    {
        $this->durationSeconds = $durationSeconds;
    }

    public function getAvgPowerWatts() : float
    {
        return $this->avgPowerWatts;
    }

    public function setAvgPowerWatts(float $avgPowerWatts = null)
    {
        $this->avgPowerWatts = $avgPowerWatts;
    }

    public function getAvgSpeedMetersPerSecond() : float
    {
        return $this->avgSpeedMetersPerSecond;
    }

    public function setAvgSpeedMetersPerSecond(float $avgSpeedMetersPerSecond = null)
    {
        $this->avgSpeedMetersPerSecond = $avgSpeedMetersPerSecond;
    }

    public function jsonSerialize() {
        return [
            'name'                    => $this->name,
            'minPercent'              => $this->minPercent,
            'maxPercent'              => $this->maxPercent,
            'durationSeconds'         => $this->durationSeconds,
            'avgPowerWatts'           => $this->avgPowerWatts,
            'avgSpeedMetersPerSecond' => $this->avgSpeedMetersPerSecond
        ];
    }
}
