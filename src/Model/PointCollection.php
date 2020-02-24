<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;
use PhpSports\Model\Lap;

class PointCollection extends \ArrayObject implements \JsonSerializable
{

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Point) {
            throw new \Exception('value must be an instance of Point');
        }

        parent::offsetSet($offset, $value);
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
        $filtered = array_filter((array) $this, function($point) use ($timeStart, $timeEnd) {
            $time = $point->getTimestamp();
            return (($time >= $timeStart) && ($time <= $timeEnd));
        });
        return new static($filtered);
    }

    public function jsonSerialize() {
        return array_values((array) $this);
    }
}
