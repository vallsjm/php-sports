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

    public function addPoint(Point $point) : PointCollection
    {
        parent::append($point);
        return $this;
    }

    public function jsonSerialize() {
        return (array) $this;
    }
}
