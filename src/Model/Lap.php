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
    const MAXDISTANCE = 999999;

    private $startedAt;
    private $name;
    private $points;
    private $analysis;
    private $distanceMeters;
    private $durationSeconds;
    private $elevationGainMeters;

    public function __construct(string $name = null, array $options = [])
    {
        $options = array_merge_recursive([
            'analysis' => [
            ]
        ], $options);

        $this->points              = new PointCollection();
        $this->analysis            = new AnalysisCollection($options['analysis']);
        $this->distanceMeters      = 0;
        $this->durationSeconds     = 0;
        $this->elevationGainMeters = 0;
        $this->startedAt           = null;
        $this->name                = $name;
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

    public function getDistanceMeters() : float
    {
        return $this->distanceMeters;
    }

    public function setDistanceMeters(float $distanceMeters) : Lap
    {
        $this->distanceMeters = $distanceMeters;
        return $this;
    }

    public function getElevationGainMeters() : float
    {
        return $this->elevationGainMeters;
    }

    public function setElevationGainMeters(float $elevationGainMeters) : Lap
    {
        $this->elevationGainMeters = $elevationGainMeters;
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

    public function createPoint(int $timestamp = null) : Point
    {
        $point = new Point($timestamp);
        return $point;
    }

    public function addPoint(Point $point) : Lap
    {
        if (count($this->points)) {
            $last = end($this->points);
            $distance = Calculate::calculateDistanceMeters($last, $point);
            if ($distance < self::MAXDISTANCE) {
                $this->distanceMeters          += $distance;
                $this->durationSeconds         += Calculate::calculateDurationSeconds($last, $point);
                $this->elevationGainMeters     += Calculate::calculateElevationGainMeters($last, $point);
            } else {
                return $this;
            }
        } else {
            if (!$this->startedAt) {
                $startedAt = new DateTime();
                $startedAt->setTimestamp($point->getTimestamp());
                $this->setStartedAt($startedAt);
            }
        }
        $this->points->addPoint($point);
        $this->analysis->analyzePoint($point);
        return $this;
    }

    public function getPoints() : PointCollection
    {
        return $this->points;
    }

    public function addAnalysis(Analysis $analysis) : Lap
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

    public function getNumPoints() : int
    {
        return $this->points->count();
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
                'distanceMeters'      => $this->distanceMeters,
                'durationSeconds'     => $this->durationSeconds,
                'elevationGainMeters' => $this->elevationGainMeters,
                'numPoints'           => $this->points->count()
            ],
            'track'    => $this->points
        ];
    }
}
