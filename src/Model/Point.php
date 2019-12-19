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
            self::clearStructure();
        };
    }

    public static function clearStructure()
    {
        self::$structure = [
            'timestamp' => true
        ];
    }

    public static function getStructure()
    {
        return array_keys(self::$structure);
    }

    public static function setStructure(array $structure)
    {
        self::$structure = array_fill_keys($structure, true);
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
        return $this->{$parameter};
    }

    public function setParameter(string $parameter, $value) : Point
    {
        $this->{$parameter} = $value;
        self::$structure[$parameter] = true;
        return $this;
    }

    public function jsonSerialize() {
        $ret = [];
        foreach (self::$structure as $key => $value) {
            $ret[] = $this->{$key};
        }
        return $ret;
    }

}
