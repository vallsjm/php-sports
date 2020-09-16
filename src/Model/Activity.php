<?php

namespace PhpSports\Model;

use PhpSports\Model\Lap;
use PhpSports\Model\LapCollection;
use PhpSports\Model\Type;
use PhpSports\Model\Analysis;
use PhpSports\Model\AnalysisCollection;
use PhpSports\Model\Point;
use PhpSports\Model\PointCollection;
use PhpSports\Model\AthleteStatus;
use PhpSports\Model\Source;
use \JsonSerializable;
use \DateTime;

class Activity implements JsonSerializable
{
    private $id;
    private $athleteStatus;
    private $source;
    private $sport;
    private $title;
    private $analysis;
    private $laps;
    private $points;
    private $startedAt;
    private $timestampOffset;

    public function __construct(string $title = null)
    {
        $this->laps            = new LapCollection();
        $this->analysis        = new AnalysisCollection();
        $this->points          = new PointCollection();
        $this->id              = null;
        $this->sport           = null;
        $this->athleteStatus   = null;
        $this->source          = null;
        $this->startedAt       = null;
        $this->timestampOffset = null;
        $this->title           = $title;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id = null)
    {
        $this->id = $id;
    }

    public function setAthleteStatus(AthleteStatus $athleteStatus)
    {
        $this->athleteStatus = $athleteStatus;
    }

    public function getAthleteStatus() : AthleteStatus
    {
        return $this->athleteStatus;
    }

    public function setSource(Source $source)
    {
        $this->source = $source;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle(string $title = null)
    {
        $this->title = $title;
    }

    public function getSport()
    {
        return $this->sport;
    }

    public function setSport(string $sport = null)
    {
        if (!is_null($sport) && (!in_array($sport, Type::SPORTS))) {
            throw new \Exception('sport value is not valid');
        }
        $this->sport = $sport;
    }

    public function addPoint(Point $point)
    {
        $this->points->addPoint($point);
    }

    public function getPoints() : PointCollection
    {
        return $this->points;
    }

    public function setPoints(PointCollection $points)
    {
        $this->points = $points;
    }

    public function addLap(Lap $lap)
    {
        $this->laps->addLap($lap);
    }

    public function getLaps() : LapCollection
    {
        return $this->laps;
    }

    public function setLaps(LapCollection $laps)
    {
        $this->laps = $laps;
    }

    public function addAnalysis(Analysis $analysis)
    {
        $this->analysis->addAnalysis($analysis);
    }

    public function getAnalysis() : AnalysisCollection
    {
        return $this->analysis;
    }

    public function setAnalysis(AnalysisCollection $analysis)
    {
        $this->analysis = $analysis;
    }

    public function getStartedAt()
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTime $startedAt = null)
    {
        $this->startedAt = $startedAt;
    }

    public function getTimestampOffset()
    {
        return $this->timestampOffset;
    }

    public function setTimestampOffset(int $timestampOffset = null)
    {
        $this->timestampOffset = $timestampOffset;
    }

    public function jsonSerialize()
    {
        return [
            'id'              => $this->id,
            'sport'           => $this->sport,
            'startedAt'       => ($this->startedAt) ? $this->startedAt->format('Y-m-d H:i:s') : null,
            'timestampOffset' => $this->timestampOffset,
            'title'           => $this->title,
            'athleteStatus'   => $this->athleteStatus,
            'source'          => $this->source,
            'analysis'        => $this->analysis,
            'laps'            => $this->laps,
            'points'          => $this->points
        ];
    }
}
