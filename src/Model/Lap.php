<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;

class Lap
{
    private $name;
    private $points;

    public function __constructor()
    {
        $this->points = [];
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : Lap
    {
        $this->name = $name;
        return $this;
    }

    public function addPoint(Point $point) : Lap
    {
        $this->points[] = $point;
        return $this;
    }
}
