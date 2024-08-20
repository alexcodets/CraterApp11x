<?php

namespace Crater\Helpers;

class Chronometer
{
    private array $start = [];

    private array $end = [];

    public function start(string $name = 'default'): void
    {
        $this->start[$name] = microtime(true);
    }

    public function end(string $name = 'default'): void
    {
        $this->end[$name] = microtime(true);
    }

    public function totalExecution(string $name = 'default'): float
    {
        return ($this->end[$name] - $this->start[$name]);
    }

    public function totalExecutionMin(string $name = 'default'): float
    {
        return ($this->totalExecution($name) / 60);
    }

    public function totalExecutionMilliseconds(string $name = 'default'): int
    {
        return (round($this->totalExecution($name), 3) * 100);
    }

    public function formattedExecutionTime(string $name = 'default'): string
    {
        $seconds = $this->totalExecution($name);
        $secondsInDay = 86400;
        list($sec, $uSec) = explode('.', round($seconds, 4));
        //$time = date('H:i:s', $sec) . ".{$uSec}";
        $time = gmdate('H:i:s', $sec).".{$uSec}";
        $days = 0;
        if ($seconds > $secondsInDay) {
            $days = (int)floor($seconds / $secondsInDay);
        }

        return "{$days}:{$time}";
    }

    public function toLogTime(string $name = 'default')
    {
        \Log::debug($name);
        \Log::debug('--------------');
        \Log::debug('  Seconds: '.$this->totalExecution($name));
        \Log::debug('  Formatted: '.$this->formattedExecutionTime($name));
        \Log::debug('--------------');
    }
}
