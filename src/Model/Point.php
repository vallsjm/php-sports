<?php

namespace PhpSports\Model;

use \JsonSerializable;

final class Point implements JsonSerializable
{
    private $timestamp;
    private $latitude;
    private $longitude;
    private $altitudeMeters;
    private $distanceMeters;
    private $speedMetersPerSecond;
    private $cadenceRPM;
    private $powerWatts;
    private $hrBPM;

    public function __construct($timestamp = null)
    {
        $this->timestamp            = $timestamp;
        $this->latitude             = null;
        $this->longitude            = null;
        $this->altitudeMeters       = null;
        $this->distanceMeters       = null;
        $this->speedMetersPerSecond = null;
        $this->cadenceRPM           = null;
        $this->powerWatts           = null;
        $this->hrBPM                = null;
    }

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

    public function getDistanceMeters() : int
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(int $distanceMeters) : Point
    {
        $this->distanceMeters = $distanceMeters;
        return $this;
    }

    public function getAltitudeMeters() : float
    {
        return $this->altitudeMeters;
    }

    public function setAlitudeMeters(float $altitudeMeters) : Point
    {
        $this->altitudeMeters = $altitudeMeters;
        return $this;
    }

    public function getCadenceRPM() : int
    {
        return $this->cadenceRPM;
    }

    public function setCadenceRPM(int $cadenceRPM) : Point
    {
        $this->cadenceRPM = $cadenceRPM;
        return $this;
    }

    public function getPowerWatts() : int
    {
        return $this->powerWatts;
    }

    public function setPowerWatts(int $powerWatts) : Point
    {
        $this->powerWatts = $powerWatts;
        return $this;
    }

    public function getHrBPM() : int
    {
        return $this->hrBPM;
    }

    public function setHrBPM(int $hrBPM) : Point
    {
        $this->hrBPM = $hrBPM;
        return $this;
    }

    public function getSpeedMetersPerSecond() : float
    {
        return $this->speedMetersPerSecond;
    }

    public function setSpeedMetersPerSecond(float $speedMetersPerSecond) : Point
    {
        $this->speedMetersPerSecond = $speedMetersPerSecond;
        return $this;
    }

    public function jsonSerialize() {
        return [
            'timestamp' => $this->timestamp,
            'distance'  => $this->distanceMeters,
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
            'altitude'  => $this->altitudeMeters,
            'cadence'   => $this->cadenceRPM,
            'power'     => $this->powerWatts,
            'hr'        => $this->hrBPM,
            'speed'     => $this->speedMetersPerSecond
        ];
    }

}
