<?php

namespace PhpSports\Model;

use PhpSports\Model\Lap;
use PhpSports\Model\LapCollection;
use \JsonSerializable;
use \DateTime;

class Activity implements JsonSerializable
{
    private $id;
    private $name;
    private $laps;
    private $distanceMeters;
    private $durationSeconds;
    private $startedAt;

    public function __construct($name = null)
    {
        $this->laps            = new LapCollection();
        $this->distanceMeters  = 0;
        $this->durationSeconds = 0;
        $this->startedAt       = null;
        $this->name            = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id = null) : Activity
    {
        $this->id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name = null) : Activity
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
        if (!$this->startedAt) {
            $this->setStartedAt($lap->getStartedAt());
        }
        $this->laps[] = $lap;
        return $this;
    }

    public function getLaps() : LapCollection
    {
        return $this->laps;
    }

    public function getStartedAt()
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTime $startedAt = null) : Activity
    {
        $this->startedAt = $startedAt;
        return $this;
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
