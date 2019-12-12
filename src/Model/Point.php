<?php

namespace PhpSports\Model;

use \JsonSerializable;

final class Point implements JsonSerializable
{
    private $timestamp;
    private $latitude;
    private $longitude;
    private $altitudeMeters;

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

    public function getAltitudeMeters() : int
    {
        return $this->altitudeMeters;
    }

    public function setAlitudeMeters(int $altitudeMeters) : Point
    {
        $this->altitudeMeters = $altitudeMeters;
        return $this;
    }

    public function jsonSerialize() {
        return [
            'timestamp'      => $this->timestamp,
            'latitude'       => $this->latitude,
            'longitude'      => $this->longitude,
            'altitudeMeters' => $this->altitudeMeters
        ];
    }

}
