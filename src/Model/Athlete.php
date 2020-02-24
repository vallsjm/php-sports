<?php

namespace PhpSports\Model;

use \JsonSerializable;

final class Athlete implements JsonSerializable
{
    private $id;
    private $hrBPM; // maxHr
    private $powerWatts; // ftp

    public function __construct()
    {
        $this->id         = null;
        $this->hrBPM      = null;
        $this->powerWatts = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id = null)
    {
        $this->id = $id;
    }

    public function getHrBPM()
    {
        return $this->hrBPM;
    }

    public function setHrBPM(int $hrBPM = null)
    {
        $this->hrBPM = $hrBPM;
    }

    public function getPowerWatts()
    {
        return $this->powerWatts;
    }

    public function setPowerWatts(int $powerWatts = null)
    {
        $this->powerWatts = $powerWatts;
    }

    public function jsonSerialize() {
        return [
            'id'         => $this->id,
            'hrBPM'      => $this->hrBPM,
            'powerWatts' => $this->powerWatts
        ];
    }
}
