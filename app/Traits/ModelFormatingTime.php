<?php

namespace Crater\Traits;

use Carbon\Carbon;

trait ModelFormatingTime
{
    public function getFormattedDateAttribute($var, $tmz = null)
    {
        return Carbon::createFromTimeStamp($var, $tmz)->toDateTimeString();
    }

    public function getFormattedTimeAttribute(int $seconds)
    {
        $secondsInDay = 86400;
        $time = gmdate("H:i:s", $seconds);
        $days = 0;
        if ($seconds > $secondsInDay) {
            $days = intval(floor($seconds / $secondsInDay));
        }
        $time = "{$days}:{$time}";

        return $time;
    }
}
