<?php

namespace PhpSports\Timer;

use Closure;
use DateTime;

final class Timer
{
    private $durationSeconds;

    public function __construct()
    {
        $this->durationSeconds = [];
    }

    public function addFunction($functionName, Closure $callback)
    {
        $startTime = microtime(true);
        $ret = $callback();
        $this->durationSeconds[$functionName] = microtime(true) - $startTime;
        return $ret;
    }

    public function getFunctionDuration($functionName)
    {
        if (isset($this->durationSeconds[$functionName])) {
            return $this->durationSeconds[$functionName];
        }
        return null;
    }

    public function printFunctionDuration($functionName)
    {
        if (isset($this->durationSeconds[$functionName])) {
            $total = $this->getTotalDuration();
            $duration = $this->durationSeconds[$functionName];
            echo "[" . round($duration, 4) ."s] [" . round($duration * 100 / $total). "%] " . $functionName . PHP_EOL;
        }
    }

    public function getTotalDuration()
    {
        $total = 0;
        foreach ($this->durationSeconds as $functionName => $duration) {
            $total += $duration;
        }
        return $total;
    }

    public function __toString() {
        $total = $this->getTotalDuration();

        $ret = '';
        foreach ($this->durationSeconds as $functionName => $duration) {
            $ret .= "[" . round($duration, 4) ."s] [" . round($duration * 100 / $total). "%] " . $functionName . PHP_EOL;
        }
        return $ret;
    }

}
