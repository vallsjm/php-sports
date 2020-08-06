<?php

namespace PhpSports\Timer;

use Closure;
use DateTime;

final class Timer
{
    private $resume;

    public function __construct()
    {
        $this->resume = [];
    }

    public function addFunction(string $functionName, Closure $callback)
    {
        $startTime = microtime(true);
        $ret = $callback();
        $this->resume[$functionName] = microtime(true) - $startTime;
        return $ret;
    }

    public function setFunctionDuration(string $functionName, float $durationSeconds)
    {
        $this->resume[$functionName] = $durationSeconds;
    }

    public function getFunctionDuration($functionName)
    {
        if (isset($this->resume[$functionName])) {
            return $this->resume[$functionName];
        }
        return null;
    }

    public function getTotalDuration()
    {
        return array_sum($this->resume);
    }

    public function getResume() : array
    {
        return $this->resume;
    }

    private function toString($break = PHP_EOL)
    {
        $total = $this->getTotalDuration();
        $ret = '';
        foreach ($this->resume as $functionName => $duration) {
            $ret .= $break . "[" . round($duration, 4) ."s] [" . round($duration * 100 / $total) . "%] " . $functionName;
        }
        $ret .= $break . "TOTAL: " . round($total, 4) ."s" . $break;
        return $ret;
    }

    public function __toString()
    {
        return $this->toString(PHP_EOL);
    }

    public function html()
    {
        return $this->toString('<br>');
    }
}
