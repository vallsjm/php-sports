<?php

namespace PhpSports\Model;

use PhpSports\Model\Schema;
use \JsonSerializable;
use \Exception;

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

    public function __construct(
        int $timestamp = null
    )
    {
        if (!self::$schema) {
            self::resetSchema();
        }

        $this->latitude             = null;
        $this->longitude            = null;
        $this->altitudeMeters       = null;
        $this->elevationMeters      = null;
        $this->distanceMeters       = null;
        $this->speedMetersPerSecond = null;
        $this->cadenceRPM           = null;
        $this->powerWatts           = null;
        $this->hrBPM                = null;

        $this->setTimestamp($timestamp);
    }

    public static function resetSchema()
    {
        self::$schema = new Schema();
        return self::$schema;
    }

    public static function setSchema(Schema $schema)
    {
        self::$schema = $schema;
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
        self::$schema->addParameter('timestamp');
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude = null)
    {
        $this->latitude = $latitude;
        self::$schema->addParameter('latitude', 5);
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude = null)
    {
        $this->longitude = $longitude;
        self::$schema->addParameter('longitude', 5);
    }

    public function getDistanceMeters()
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(float $distanceMeters = null)
    {
        $this->distanceMeters = $distanceMeters;
        self::$schema->addParameter('distanceMeters');
    }

    public function getDistanceKilometers()
    {
        return $this->distanceMeters / 1000;
    }

    public function setDistanceKilometers(float $distanceKilometers = null)
    {
        $this->distanceMeters = $distanceKilometers * 1000;
        self::$schema->addParameter('distanceMeters');
    }

    public function getElevationMeters()
    {
        return $this->elevationMeters;
    }

    public function setElevationMeters(float $elevationMeters = null)
    {
        $this->elevationMeters = $elevationMeters;
        self::$schema->addParameter('elevationMeters');
    }

    public function getAltitudeMeters()
    {
        return $this->altitudeMeters;
    }

    public function setAltitudeMeters(float $altitudeMeters = null)
    {
        $this->altitudeMeters = $altitudeMeters;
        self::$schema->addParameter('altitudeMeters');
    }

    public function getCadenceRPM()
    {
        return $this->cadenceRPM;
    }

    public function setCadenceRPM(int $cadenceRPM = null)
    {
        $this->cadenceRPM = $cadenceRPM;
        self::$schema->addParameter('cadenceRPM');
    }

    public function getPowerWatts()
    {
        return $this->powerWatts;
    }

    public function setPowerWatts(int $powerWatts = null)
    {
        $this->powerWatts = $powerWatts;
        self::$schema->addParameter('powerWatts');
    }

    public function getHrBPM()
    {
        return $this->hrBPM;
    }

    public function setHrBPM(int $hrBPM = null)
    {
        $this->hrBPM = $hrBPM;
        self::$schema->addParameter('hrBPM');
    }

    public function getSpeedMetersPerSecond()
    {
        return $this->speedMetersPerSecond;
    }

    public function setSpeedMetersPerSecond(float $speedMetersPerSecond = null)
    {
        $this->speedMetersPerSecond = $speedMetersPerSecond;
        self::$schema->addParameter('speedMetersPerSecond');
    }

    public function getSpeedKilometersPerHour()
    {
        return $this->speedMetersPerSecond * 3.6;
    }

    public function setSpeedKilometersPerHour(float $speedKilometersPerHour = null)
    {
        $this->speedMetersPerSecond = $speedKilometersPerHour / 3.6;
        self::$schema->addParameter('speedMetersPerSecond');
    }

    public function getParameter(string $parameter)
    {
        return $this->{$parameter};
    }

    public function setParameter(string $parameter, $value)
    {
        $this->{$parameter} = $value;
        self::$schema->addParameter($parameter);
    }

    public function jsonSerialize() {
        $ret = [];
        foreach (self::$schema->getParameters() as $parameter => $accurency) {
            $ret[] = round($this->{$parameter}, $accurency);
        }
        return $ret;
    }

}
