<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;
use PhpSports\Analysis\Calculate;

final class Lap
{
    private $name;
    private $points;
    private $distanceMeters;
    private $durationSeconds;

    public function __construct()
    {
        $this->points          = [];
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
            $this->distanceMeters  += Calculate::calculateDistance($last, $point);
            $this->durationSeconds += Calculate::calculateDuration($last, $point);
        }
        $this->points[] = $point;
        return $this;
    }
}
