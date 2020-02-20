<?php

namespace PhpSports\Model;

use \JsonSerializable;

final class Athlete implements JsonSerializable
{
    private $id;
    private $hrBPM; // maxHr
    private $ftpWatts; // ftp

    public function __construct(string $name = null)
    {
        $this->distanceMeters      = 0;
        $this->durationSeconds     = 0;
        $this->elevationGainMeters = 0;
        $this->timestampFrom       = null;
        $this->timestampTo         = null;
        $this->name                = $name;
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

    public function jsonSerialize() {
        return [
            'name'                => $this->name,
            'distanceMeters'      => $this->distanceMeters,
            'durationSeconds'     => $this->durationSeconds,
            'elevationGainMeters' => $this->elevationGainMeters,
            'timestampFrom'       => $this->timestampFrom,
            'timestampTo'         => $this->timestampTo
        ];
    }
}
