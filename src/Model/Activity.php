<?php

namespace PhpSports\Model;

use PhpSports\Model\Lap;
use PhpSports\Model\LapCollection;
use PhpSports\Model\Type;
use PhpSports\Model\Analysis;
use PhpSports\Model\AnalysisCollection;
use \JsonSerializable;
use \DateTime;

class Activity implements JsonSerializable
{
    private $id;
    private $sport;
    private $name;
    private $laps;
    private $distanceMeters;
    private $durationSeconds;
    private $numPoints;
    private $analysis;
    private $startedAt;

    public function __construct($name = null)
    {
        $this->laps            = new LapCollection();
        $this->analysis        = new AnalysisCollection();
        $this->id              = null;
        $this->sport           = null;
        $this->distanceMeters  = 0;
        $this->durationSeconds = 0;
        $this->numPoints       = 0;
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

    public function getSport()
    {
        return $this->sport;
    }

    public function setSport(string $sport = null) : Activity
    {
        if (!in_array($sport, Type::SPORTS)) {
            throw new \Exception('sport value is not valid');
        }
        $this->sport = $sport;
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

    public function getNumPoints() : int
    {
        return $this->numPoints;
    }

    public function addLap(Lap $lap) : Activity
    {
        $this->distanceMeters  += $lap->getDistanceMeters();
        $this->durationSeconds += $lap->getDurationSeconds();
        $this->analysis->merge($lap->getAnalysis());
        if (!$this->startedAt) {
            $this->setStartedAt($lap->getStartedAt());
        }
        $this->laps->addLap($lap);
        $this->numPoints += $lap->getNumPoints();
        return $this;
    }

    public function getLaps() : LapCollection
    {
        return $this->laps;
    }

    public function addAnalysis(Analysis $analysis) : Activity
    {
        $this->analysis->addAnalysis($analysis);
        return $this;
    }

    public function getAnalysis() : AnalysisCollection
    {
        return $this->analysis;
    }

    public function getAnalysisOrCreate(string $parameter) : Analysis
    {
        return $this->analysis->getAnalysisOrCreate($parameter);
    }

    public function getAnalysisOrNull(string $parameter)
    {
        return $this->analysis->getAnalysisOrNull($parameter);
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
            'id'    => $this->id,
            'sport' => $this->sport,
            'name'  => $this->name,
            'resume' => [
                'distanceMeters'  => $this->distanceMeters,
                'durationSeconds' => $this->durationSeconds,
                'numPoints'       => $this->numPoints,
            ],
            'analysis' => $this->analysis,
            'laps'     => $this->laps
        ];
    }

}
