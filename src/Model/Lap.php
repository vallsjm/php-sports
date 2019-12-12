<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;
use PhpSports\Model\PointCollection;
use PhpSports\Analysis\Calculate;
use \JsonSerializable;

final class Lap implements JsonSerializable
{
    private $name;
    private $points;
    private $distanceMeters;
    private $durationSeconds;

    public function __construct()
    {
        $this->points          = new PointCollection();
        $this->distanceMeters  = 0;
        $this->durationSeconds = 0;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : Lap
    {
        $this->name = $name;
        return $this;
    }

    public function getDistanceMeters() : int
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(int $distanceMeters) : Lap
    {
        $this->distanceMeters = $distanceMeters;
        return $this;
    }

    public function getDurationSeconds() : int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(int $durationSeconds) : Lap
    {
        $this->durationSeconds = $durationSeconds;
        return $this;
    }

    public function addPoint(Point $point) : Lap
    {
        if (count($this->points)) {
            $last = end($this->points);
            $this->distanceMeters  += Calculate::calculateDistanceMeters($last, $point);
            $this->durationSeconds += Calculate::calculateDurationSeconds($last, $point);
        }
        $this->points[] = $point;
        return $this;
    }

    public function getPoints() : PointCollection
    {
        return $this->points;
    }

    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'resume' => [
                'distanceMeters'  => $this->distanceMeters,
                'durationSeconds' => $this->durationSeconds
            ],
            'points' => $this->points
        ];
    }
}
