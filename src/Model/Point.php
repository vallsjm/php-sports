<?php

namespace PhpSports\Model;

class Point
{
    private $latitude;
    private $longitude;
    private $distanceMeters;
    private $durationSeconds;
    private $elevationMeters;

    public function getLatitude() : float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude) : Point
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude() : float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude) : Point
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getDistanceMeters() : int
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(int $distanceMeters) : Point
    {
        $this->distanceMeters = $distanceMeters;
        return $this;
    }

    public function getDurationSeconds() : int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(int $durationSeconds) : Point
    {
        $this->durationSeconds = $durationSeconds;
        return $this;
    }

    public function getElevationMeters() : int
    {
        return $this->elevationMeters;
    }

    public function setElevationMeters(int $elevationMeters) : Point
    {
        $this->elevationMeters = $elevationMeters;
        return $this;
    }

}
