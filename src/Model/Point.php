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

    private static $schema;

    public function __construct(int $timestamp = null)
    {
        $this->latitude             = null;
        $this->longitude            = null;
        $this->altitudeMeters       = null;
        $this->elevationMeters      = null;
        $this->distanceMeters       = null;
        $this->speedMetersPerSecond = null;
        $this->cadenceRPM           = null;
        $this->powerWatts           = null;
        $this->hrBPM                = null;

        if (is_null(self::$schema)) {
            self::$schema = [];
        }

        $this->setTimestamp($timestamp);
    }

    public static function getSchema()
    {
        return self::$schema;
    }

    public function getTimestamp() : int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp = null)
    {
        $this->timestamp = $timestamp;
        self::$schema['timestamp'] = true;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude = null)
    {
        $this->latitude = $latitude;
        self::$schema['latitude'] = true;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude = null)
    {
        $this->longitude = $longitude;
        self::$schema['longitude'] = true;
    }

    public function getDistanceMeters()
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(float $distanceMeters = null)
    {
        $this->distanceMeters = $distanceMeters;
        self::$schema['distanceMeters'] = true;
    }

    public function getElevationMeters()
    {
        return $this->elevationMeters;
    }

    public function setElevationMeters(float $elevationMeters = null)
    {
        $this->elevationMeters = $elevationMeters;
        self::$schema['elevationMeters'] = true;
    }

    public function getAltitudeMeters()
    {
        return $this->altitudeMeters;
    }

    public function setAltitudeMeters(float $altitudeMeters = null)
    {
        $this->altitudeMeters = $altitudeMeters;
        self::$schema['altitudeMeters'] = true;
    }

    public function getCadenceRPM()
    {
        return $this->cadenceRPM;
    }

    public function setCadenceRPM(int $cadenceRPM = null)
    {
        $this->cadenceRPM = $cadenceRPM;
        self::$schema['cadenceRPM'] = true;
    }

    public function getPowerWatts()
    {
        return $this->powerWatts;
    }

    public function setPowerWatts(int $powerWatts = null)
    {
        $this->powerWatts = $powerWatts;
        self::$schema['powerWatts'] = true;
    }

    public function getHrBPM()
    {
        return $this->hrBPM;
    }

    public function setHrBPM(int $hrBPM = null)
    {
        $this->hrBPM = $hrBPM;
        self::$schema['hrBPM'] = true;
    }

    public function getSpeedMetersPerSecond()
    {
        return $this->speedMetersPerSecond;
    }

    public function setSpeedMetersPerSecond(float $speedMetersPerSecond = null)
    {
        $this->speedMetersPerSecond = $speedMetersPerSecond;
        self::$schema['speedMetersPerSecond'] = true;
    }

    public function getParameter(string $parameter)
    {
        return $this->{$parameter};
    }

    public function setParameter(string $parameter, $value)
    {
        $this->{$parameter} = $value;
        self::$schema[$parameter] = true;
    }

    public function jsonSerialize() {
        $point = [
            'timestamp' => $this->timestamp
        ];

        if ($this->latitude) {
            $point['latitude'] = $this->latitude;
        }
        if ($this->longitude) {
            $point['longitude'] = $this->longitude;
        }
        if ($this->altitudeMeters) {
            $point['altitudeMeters'] = $this->altitudeMeters;
        }
        if ($this->elevationMeters) {
            $point['elevationMeters'] = $this->elevationMeters;
        }
        if ($this->distanceMeters) {
            $point['distanceMeters'] = $this->distanceMeters;
        }
        if ($this->speedMetersPerSecond) {
            $point['speedMetersPerSecond'] = $this->speedMetersPerSecond;
        }
        if ($this->cadenceRPM) {
            $point['cadenceRPM'] = $this->cadenceRPM;
        }
        if ($this->powerWatts) {
            $point['powerWatts'] = $this->powerWatts;
        }
        if ($this->hrBPM) {
            $point['hrBPM'] = $this->hrBPM;
        }

        return $point;
    }

}
