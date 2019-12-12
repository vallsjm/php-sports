<?php

namespace PhpSports\Model;

use PhpSports\Model\Activity;
use \ArrayAccess;
use \JsonSerializable;

class ActivitiesArray implements ArrayAccess, JsonSerializable
{
    private $container = [];

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Activity) {
            throw new Exception('value must be an instance of Activity');
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

    public function addActivity(Activity $activity) : ActivitiesArray
    {
        $this->container[] = $activity;
        return $this;
    }

    public function jsonSerialize() {
        return $this->container;
    }
}
