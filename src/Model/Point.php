<?php

namespace PhpSports\Model;

use \JsonSerializable;

final class Point implements JsonSerializable
{
    private $timestamp;
    private $latitude;
    private $longitude;
    private $altitudeMeters;
    private $elevationMeters;
    private $distanceMeters;
    private $speedMetersPerSecond;
    private $cadenceRPM;
    private $powerWatts;
    private $hrBPM;

    public function __construct(int $timestamp = null)
    {
        $this->timestamp            = $timestamp;
        $this->latitude             = null;
        $this->longitude            = null;
        $this->altitudeMeters       = null;
        $this->elevationMeters      = null;
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

    public function setTimestamp(int $timestamp = null)
    {
        $this->timestamp = $timestamp;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude = null)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude = null)
    {
        $this->longitude = $longitude;
    }

    public function getDistanceMeters()
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(float $distanceMeters = null)
    {
        $this->distanceMeters = $distanceMeters;
    }

    public function getElevationMeters()
    {
        return $this->elevationMeters;
    }

    public function setElevationMeters(float $elevationMeters = null)
    {
        $this->elevationMeters = $elevationMeters;
    }

    public function getAltitudeMeters()
    {
        return $this->altitudeMeters;
    }

    public function setAltitudeMeters(float $altitudeMeters = null)
    {
        $this->altitudeMeters = $altitudeMeters;
    }

    public function getCadenceRPM()
    {
        return $this->cadenceRPM;
    }

    public function setCadenceRPM(int $cadenceRPM = null)
    {
        $this->cadenceRPM = $cadenceRPM;
    }

    public function getPowerWatts()
    {
        return $this->powerWatts;
    }

    public function setPowerWatts(int $powerWatts = null)
    {
        $this->powerWatts = $powerWatts;
    }

    public function getHrBPM()
    {
        return $this->hrBPM;
    }

    public function setHrBPM(int $hrBPM = null)
    {
        $this->hrBPM = $hrBPM;
    }

    public function getSpeedMetersPerSecond()
    {
        return $this->speedMetersPerSecond;
    }

    public function setSpeedMetersPerSecond(float $speedMetersPerSecond = null)
    {
        $this->speedMetersPerSecond = $speedMetersPerSecond;
    }

    public function jsonSerialize() {
        return [
            'timestamp'            => $this->timestamp,
            'latitude'             => $this->latitude,
            'longitude'            => $this->longitude,
            'altitudeMeters'       => $this->altitudeMeters,
            'elevationMeters'      => $this->elevationMeters,
            'distanceMeters'       => $this->distanceMeters,
            'speedMetersPerSecond' => $this->speedMetersPerSecond,
            'cadenceRPM'           => $this->cadenceRPM,
            'powerWatts'           => $this->powerWatts,
            'hrBPM'                => $this->hrBPM
        ];
    }

}
