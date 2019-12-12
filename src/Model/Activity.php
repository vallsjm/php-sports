<?php

namespace PhpSports\Model;

use PhpSports\Model\Lap;
use PhpSports\Model\LapsArray;
use \JsonSerializable;

class Activity implements JsonSerializable
{
    private $name;
    private $laps;
    private $distanceMeters;
    private $durationSeconds;

    public function __construct()
    {
        $this->laps            = new LapsArray();
        $this->distanceMeters  = 0;
        $this->durationSeconds = 0;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : Activity
    {
        $this->name = $name;
        return $this;
    }

    public function getDistanceMeters() : int
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(int $distanceMeters) : Activity
    {
        $this->distanceMeters = $distanceMeters;
        return $this;
    }

    public function getDurationSeconds() : int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(int $durationSeconds) : Activity
    {
        $this->durationSeconds = $durationSeconds;
        return $this;
    }

    public function addLap(Lap $lap) : Activity
    {
        $this->distanceMeters  += $lap->getDistanceMeters();
        $this->durationSeconds += $lap->getDurationSeconds();
        $this->laps[] = $lap;
        return $this;
    }

    public function getLaps() : LapsArray
    {
        return $this->laps;
    }

    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'resume' => [
                'distanceMeters'  => $this->distanceMeters,
                'durationSeconds' => $this->durationSeconds
            ],
            'laps' => $this->laps
        ];
    }

}
