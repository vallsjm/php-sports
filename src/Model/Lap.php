<?php

namespace PhpSports\Model;

use PhpSports\Model\AnalysisCollection;
use PhpSports\Model\Analysis;
use PhpSports\Model\Point;
use \JsonSerializable;
use \DateTime;

final class Lap implements JsonSerializable
{
    private $id;
    private $name;
    private $analysis;
    private $timestampFrom;
    private $timestampTo;

    public function __construct(
        $id = null,
        string $name = null,
        int $timestampFrom = null,
        int $timestampTo = null
    )
    {
        $this->id            = $id;
        $this->timestampFrom = $timestampFrom;
        $this->timestampTo   = $timestampTo;
        $this->name          = $name;
        $this->analysis      = new AnalysisCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id = null)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name = null)
    {
        $this->name = $name;
    }

    public function getTimestampFrom() : int
    {
        return $this->timestampFrom;
    }

    public function setTimestampFrom(int $timestampFrom)
    {
        $this->timestampFrom = $timestampFrom;
    }

    public function getTimestampTo() : int
    {
        return $this->timestampTo;
    }

    public function setTimestampTo(int $timestampTo)
    {
        $this->timestampTo = $timestampTo;
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

    public function addPoint(Point $point)
    {
        $this->timestampFrom = (!$this->timestampFrom) ? $point->getTimestamp() : min($point->getTimestamp(), $this->timestampFrom);
        $this->timestampTo   = (!$this->timestampTo) ? $point->getTimestamp()   : max($point->getTimestamp(), $this->timestampTo);
    }

    public function jsonSerialize() {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'analysis'      => $this->analysis,
            'timestampFrom' => $this->timestampFrom,
            'timestampTo'   => $this->timestampTo
        ];
    }
}
