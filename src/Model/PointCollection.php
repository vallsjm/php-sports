<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;

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

    public function filterByTimestamp(int $timestampFrom, int $timestampTo) : PointCollection
    {
        reset($this);
        $firstTimestamp = key($this);
        end($this);
        $lastTimestamp = key($this);

        if (($timestampFrom <= $firstTimestamp) &&
            ($timestampTo >= $lastTimestamp)) {
            return $this;
        } 

        $filtered = array_slice(
            $this, 
            $timestampFrom - $firstTimestamp, 
            $timestampTo - $timestampFrom, 
            true
        );

        return new static($filtered);
    }

    public function jsonSerialize() {
        return array_values((array) $this);
    }
}
