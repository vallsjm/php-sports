<?php

namespace PhpSports\Model;

final class Point
{
    private $timestamp;
    private $latitude;
    private $longitude;
    private $elevationMeters;

    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp) : Point
    {
        $this->timestamp = $timestamp;
        return $this;
    }

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
