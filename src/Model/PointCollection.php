<?php

namespace PhpSports\Model;

use PhpSports\Model\Schema;
use PhpSports\Model\Point;
use PhpSports\Model\Lap;
use \Closure;

class PointCollection extends \ArrayObject implements \JsonSerializable
{
    private $schema;

    public function __construct($points = [])
    {
        if ($point = current($points)) {
            $this->schema = $point::getSchema();
        } else {
            $this->schema = Point::resetSchema();
        }
        parent::__construct($points);
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Point) {
            throw new \Exception('value must be an instance of Point');
        }

        parent::offsetSet($offset, $value);
    }

    public function getSchema() : Schema
    {
        return $this->schema;
    }

    public function addPoint(Point $point)
    {
        $pos = $point->getTimestamp();
        $this[$pos] = $point;
    }

    public function filterByLap(Lap $lap) : PointCollection
    {
        $timeStart = $lap->getTimestampFrom();
        $timeEnd   = $lap->getTimestampTo();
        return $this->filterByTimestamp(
            $lap->getTimestampFrom(),
            $lap->getTimestampTo()
        );
    }

    public function filterByTimestamp(int $timestampFrom, int $timestampTo) : PointCollection
    {
        $items = (array) $this;

        reset($items);
        $firstTimestamp = key($items);
        end($items);
        $lastTimestamp = key($items);

        if (($timestampFrom <= $firstTimestamp) &&
            ($timestampTo >= $lastTimestamp)) {
            return $this;
        }

        return $this->filter(function ($point) use ($timestampFrom, $timestampTo) {
            $time = $point->getTimestamp();
            return (($time >= $timestampFrom) && ($time <= $timestampTo));
        });
    }

    public function filter(Closure $filterFunction) : PointCollection
    {
        $items = (array) $this;
        $filtered = array_filter($items, $filterFunction);
        return new static($filtered);
    }

    public function jsonSerialize()
    {
        Point::setSchema($this->schema);
        return [
            'schema' => $this->schema,
            'points' => array_values((array) $this)
        ];
    }
}
