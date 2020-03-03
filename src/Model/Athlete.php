<?php

namespace PhpSports\Model;

use \JsonSerializable;

final class Athlete implements JsonSerializable
{
    private $id;
    private $maxHrBPM; // maxHr
    private $ftpPowerWatts; // ftp
    private $gender;
    private $ageYears;
    private $weightKg;
    private $heightMetters;

    public function __construct()
    {
        $this->id            = null;
        $this->maxHrBPM      = null;
        $this->ftpPowerWatts = null;
        $this->gender        = null;
        $this->ageYears      = null;
        $this->weightKg      = null;
        $this->heightMetters = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id = null)
    {
        $this->id = $id;
    }

    public function getMaxHrBPM()
    {
        return $this->maxHrBPM;
    }

    public function setMaxHrBPM(int $maxHrBPM = null)
    {
        $this->maxHrBPM = $maxHrBPM;
    }

    public function getFtpPowerWatts()
    {
        return $this->ftpPowerWatts;
    }

    public function setFtpPowerWatts(int $ftpPowerWatts = null)
    {
        $this->ftpPowerWatts = $ftpPowerWatts;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender(string $gender = null)
    {
        $this->gender = $gender;
    }

    public function getAgeYears()
    {
        return $this->ageYears;
    }

    public function setAgeYears(int $ageYears = null)
    {
        $this->ageYears = $ageYears;
    }

    public function getWeightKg()
    {
        return $this->weightKg;
    }

    public function setWeightKg(int $weightKg = null)
    {
        $this->weightKg = $weightKg;
    }

    public function getHeightMetters()
    {
        return $this->heightMetters;
    }

    public function setHeightMetters(int $heightMetters = null)
    {
        $this->heightMetters = $heightMetters;
    }

    public function jsonSerialize() {
        return [
            'id'            => $this->id,
            'maxHrBPM'      => $this->maxHrBPM,
            'ftpPowerWatts' => $this->ftpPowerWatts,
            'gender'        => $this->gender,
            'ageYears'      => $this->ageYears,
            'weightKg'      => $this->weightKg,
            'heightMetters' => $this->heightMetters
        ];
    }
}
