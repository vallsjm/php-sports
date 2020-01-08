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
    private $elevationGainMeters;
    private $durationSeconds;
    private $numPoints;
    private $analysis;
    private $startedAt;
    private $options;

    public function __construct(string $name = null, array $options = [])
    {
        $default = [
            'analysis' => [
                'altitudeMeters'       => [],
                'elevationMeters'      => [],
                'speedMetersPerSecond' => [5, 60, 300, 1200, 3600],
                'hrBPM'                => [5, 60, 300, 1200, 3600],
                'cadenceRPM'           => [5, 60, 300, 1200, 3600],
                'powerWatts'           => [5, 60, 300, 1200, 3600]
            ]
        ];

        $this->options             = array_merge_recursive($default, $options);

        $this->laps                = new LapCollection();
        $this->analysis            = new AnalysisCollection($this->options['analysis']);
        $this->id                  = null;
        $this->sport               = null;
        $this->distanceMeters      = 0;
        $this->durationSeconds     = 0;
        $this->elevationGainMeters = 0;
        $this->numPoints           = 0;
        $this->startedAt           = null;
        $this->name                = $name;
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
        if (!is_null($sport) && (!in_array($sport, Type::SPORTS))) {
            throw new \Exception('sport value is not valid');
        }
        $this->sport = $sport;
        return $this;
    }

    public function getDistanceMeters() : float
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(float $distanceMeters) : Activity
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

    public function getElevationGainMeters() : float
    {
        return $this->elevationGainMeters;
    }

    public function setElevationGainMeters(float $elevationGainMeters) : Activity
    {
        $this->elevationGainMeters = $elevationGainMeters;
        return $this;
    }

    public function getNumPoints() : int
    {
        return $this->numPoints;
    }

    public function createLap(string $name = null) : Lap
    {
        $lap = new Lap($name, $this->options);
        return $lap;
    }

    public function addLap(Lap $lap) : Activity
    {
        $this->distanceMeters       += $lap->getDistanceMeters();
        $this->durationSeconds      += $lap->getDurationSeconds();
        $this->elevationGainMeters  += $lap->getElevationGainMeters();
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

    public function getAnalysisOrCreate(string $parameter, int $intervalTimeSeconds = 0) : Analysis
    {
        return $this->analysis->getAnalysisOrCreate($parameter, $intervalTimeSeconds);
    }

    public function getAnalysisOrNull(string $parameter, int $intervalTimeSeconds = 0)
    {
        return $this->analysis->getAnalysisOrNull($parameter, $intervalTimeSeconds);
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
            'startedAt'  => ($this->startedAt) ? $this->startedAt->format('Y-m-d H:i:s') : null,
            'resume' => [
                'distanceMeters'      => $this->distanceMeters,
                'durationSeconds'     => $this->durationSeconds,
                'elevationGainMeters' => $this->elevationGainMeters,
                'numLaps'             => $this->laps->count(),
                'numPoints'           => $this->numPoints
            ],
            'analysis' => $this->analysis,
            'laps'     => $this->laps
        ];
    }

}
