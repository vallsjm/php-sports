<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;
use PhpSports\Model\PointCollection;
use PhpSports\Model\Analysis;
use PhpSports\Model\AnalysisCollection;
use PhpSports\Analysis\Calculate;
use \JsonSerializable;
use \DateTime;

final class Lap implements JsonSerializable
{
    private $startedAt;
    private $name;
    private $points;
    private $analysis;
    private $distanceMillimeters;
    private $durationSeconds;

    public function __construct($name = null)
    {
        $this->points              = new PointCollection();
        $this->analysis            = new AnalysisCollection();
        $this->distanceMillimeters = 0;
        $this->durationSeconds     = 0;
        $this->startedAt           = null;
        $this->name                = $name;

        Point::clearStructure();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name = null) : Lap
    {
        $this->name = $name;
        return $this;
    }

    public function getDistanceMeters() : int
    {
        return round($this->distanceMillimeters / 1000);
    }

    public function setDistanceMeters(int $distanceMeters) : Lap
    {
        $this->distanceMillimeters = $distanceMeters * 1000;
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
            $this->distanceMillimeters += Calculate::calculateDistanceMillimeters($last, $point);
            $this->durationSeconds     += Calculate::calculateDurationSeconds($last, $point);
        } else {
            if (!$this->startedAt) {
                $startedAt = new DateTime();
                $startedAt->setTimestamp($point->getTimestamp());
                $this->setStartedAt($startedAt);
            }
        }
        $this->analysis->analyze($point);
        $this->points[] = $point;
        return $this;
    }

    public function getPoints() : PointCollection
    {
        return $this->points;
    }

    public function addAnalysis(Analysis $analysis) : Lap
    {
        $this->analysis[] = $analysis;
        return $this;
    }

    public function getAnalysis() : AnalysisCollection
    {
        return $this->analysis;
    }

    public function getStartedAt()
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTime $startedAt = null) : Lap
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function jsonSerialize() {
        return [
            'name' => $this->name,
            'resume' => [
                'distanceMeters'  => $this->getDistanceMeters(),
                'durationSeconds' => $this->durationSeconds
            ],
            'analysis'  => $this->analysis,
            'structure' => Point::getStructure(),
            'points'    => $this->points
        ];
    }
}
