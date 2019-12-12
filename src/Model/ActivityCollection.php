<?php

namespace PhpSports\Model;

use PhpSports\Model\Activity;
use \ArrayAccess;
use \JsonSerializable;

class ActivityCollection extends \ArrayObject implements \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Activity) {
            throw new \Exception('value must be an instance of Activity');
        }

        parent::offsetSet($offset, $value);
    }

    public function addActivity(Activity $activity) : ActivityCollection
    {
        parent::append($activity);
        return $this;
    }

    public function jsonSerialize() {
        return (array) $this;
    }
}
