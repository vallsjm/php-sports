<?php

namespace PhpSports\Model;

use PhpSports\Model\Lap;
use \ArrayAccess;
use \JsonSerializable;

class LapsArray implements ArrayAccess, JsonSerializable
{
    private $container = [];

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Lap) {
            throw new Exception('value must be an instance of Lap');
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

    public function addLap(Lap $lap) : LapsArray
    {
        $this->container[] = $lap;
        return $this;
    }

    public function jsonSerialize() {
        return $this->container;
    }
}
