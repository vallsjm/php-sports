<?php

namespace PhpSports\Model;

use PhpSports\Model\Lap;

class Activity
{
    private $name;
    private $laps;

    public function __constructor()
    {
        $this->laps = [];
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : Activity
    {
        $this->name = $name;
        return $this;
    }

    public function addLap(Lap $lap) : Activity
    {
        $this->laps[] = $lap;
        return $this;
    }
}
