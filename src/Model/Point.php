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
    private static $structure;

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

        if (is_null(self::$structure)) {
            self::$structure = [];
        };
    }

    public static function clearStructure()
    {
        self::$structure = [];
    }

    public static function getStructure()
    {
        return array_keys(self::$structure);
    }

    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp) : Point
    {
        $this->timestamp = $timestamp;
        self::$structure['timestamp'] = true;
        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude) : Point
    {
        $this->latitude = $latitude;
        self::$structure['latitude'] = true;
        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude) : Point
    {
        $this->longitude = $longitude;
        self::$structure['longitude'] = true;
        return $this;
    }

    public function getDistanceMeters()
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(float $distanceMeters) : Point
    {
        $this->distanceMeters = $distanceMeters;
        self::$structure['distanceMeters'] = true;
        return $this;
    }

    public function getAltitudeMeters()
    {
        return $this->altitudeMeters;
    }

    public function setAltitudeMeters(float $altitudeMeters) : Point
    {
        $this->altitudeMeters = $altitudeMeters;
        self::$structure['altitudeMeters'] = true;
        return $this;
    }

    public function getCadenceRPM()
    {
        return $this->cadenceRPM;
    }

    public function setCadenceRPM(int $cadenceRPM) : Point
    {
        $this->cadenceRPM = $cadenceRPM;
        self::$structure['cadenceRPM'] = true;
        return $this;
    }

    public function getPowerWatts()
    {
        return $this->powerWatts;
    }

    public function setPowerWatts(int $powerWatts) : Point
    {
        $this->powerWatts = $powerWatts;
        self::$structure['powerWatts'] = true;
        return $this;
    }

    public function getHrBPM()
    {
        return $this->hrBPM;
    }

    public function setHrBPM(int $hrBPM) : Point
    {
        $this->hrBPM = $hrBPM;
        self::$structure['hrBPM'] = true;
        return $this;
    }

    public function getSpeedMetersPerSecond()
    {
        return $this->speedMetersPerSecond;
    }

    public function setSpeedMetersPerSecond(float $speedMetersPerSecond) : Point
    {
        $this->speedMetersPerSecond = $speedMetersPerSecond;
        self::$structure['speedMetersPerSecond'] = true;
        return $this;
    }

    public function getParameter(string $parameter)
    {
        $map = [
            'HR'       => $this->hrBPM,
            'CADENCE'  => $this->cadenceRPM,
            'POWER'    => $this->powerWatts,
            'DISTANCE' => $this->distanceMeters,
            'SPEED'    => $this->speedMetersPerSecond,
            'ALTITUDE' => $this->altitudeMeters
        ];

        if (isset($map[$parameter])) {
            return $map[$parameter];
        }

        return null;
    }

    public function jsonSerialize() {
        $ret = [];
        foreach (self::$structure as $key => $value) {
            $ret[$key] = $this->{$key};
        }
        return $ret;
    }

}
