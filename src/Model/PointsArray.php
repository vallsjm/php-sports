<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;
use \ArrayAccess;
use \JsonSerializable;

class PointsArray implements ArrayAccess, JsonSerializable
{
    private $container = [];

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Lap) {
            throw new Exception('value must be an instance of Point');
        }

        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function addPoint(Point $point) : PointsArray
    {
        $this->container[] = $point;
        return $this;
    }

    public function jsonSerialize() {
        return $this->container;
    }
}
