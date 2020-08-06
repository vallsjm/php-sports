<?php

namespace PhpSports\Model;

use PhpSports\Model\Lap;

class LapCollection extends \ArrayObject implements \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Lap) {
            throw new \Exception('value must be an instance of Lap');
        }

        parent::offsetSet($offset, $value);
    }

    public function addLap(Lap $lap)
    {
        parent::append($lap);
    }

    public function jsonSerialize()
    {
        return (array) $this;
    }
}
