<?php

namespace PhpSports\Model;

use PhpSports\Model\Analysis;
use PhpSports\Model\AnalysisCollection;
use \JsonSerializable;
use \DateTime;

final class Lap implements JsonSerializable
{
    private $name;
    private $analysis;
    private $timestampFrom;
    private $timestampTo;
    private $distanceMeters;
    private $durationSeconds;
    private $elevationGainMeters;

    public function __construct(
        string $name = null,
        int $timestampFrom,
        int $timestampTo
    )
    {
        $this->distanceMeters      = null;
        $this->durationSeconds     = null;
        $this->elevationGainMeters = null;
        $this->timestampFrom       = $timestampFrom;
        $this->timestampTo         = $timestampTo;
        $this->name                = $name;
        $this->analysis            = new AnalysisCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name = null)
    {
        $this->name = $name;
    }

    public function getDistanceMeters() : float
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(float $distanceMeters)
    {
        $this->distanceMeters = $distanceMeters;
    }

    public function getElevationGainMeters() : float
    {
        return $this->elevationGainMeters;
    }

    public function setElevationGainMeters(float $elevationGainMeters)
    {
        $this->elevationGainMeters = $elevationGainMeters;
    }

    public function getDurationSeconds() : int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(int $durationSeconds)
    {
        $this->durationSeconds = $durationSeconds;
    }

    public function getTimestampFrom() : int
    {
        return $this->timestampFrom;
    }

    public function setTimestampFrom(int $timestampFrom)
    {
        $this->timestampFrom = $timestampFrom;
    }

    public function getTimestampTo() : int
    {
        return $this->timestampTo;
    }

    public function setTimestampTo(int $timestampTo)
    {
        $this->timestampTo = $timestampTo;
    }

    public function addAnalysis(Analysis $analysis)
    {
        $this->analysis->addAnalysis($analysis);
    }

    public function getAnalysis() : AnalysisCollection
    {
        return $this->analysis;
    }

    public function setAnalysis(AnalysisCollection $analysis)
    {
        $this->analysis = $analysis;
    }

    public function jsonSerialize() {
        return [
            'name'          => $this->name,
            'analysis'      => $this->analysis,
            'timestampFrom' => $this->timestampFrom,
            'timestampTo'   => $this->timestampTo
        ];
    }
}
