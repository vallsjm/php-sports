<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParse
{
    private $duration;
    private $timeStart;

    public function getDuration() : float
    {
        return ($this->duration) ? $this->duration : 0;
    }

    public function startTimer() : BaseParse
    {
        $this->timeStart = microtime(true);
        return $this;
    }

    public function stopTimer() : BaseParse
    {
        $this->duration = microtime(true) - $this->timeStart;
        return $this;
    }

    public function stopTimerAndReturn($data)
    {
        $this->duration = microtime(true) - $this->timeStart;
        return $data;
    }
}
