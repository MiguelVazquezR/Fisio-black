<?php

namespace App\Support;

final class TimeLabel
{
    /**
     * Convert a 24 hour time ("18:00" or "18:00:00") into a 12 hour label.
     */
    public static function twelveHour(string $time): string
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})/', $time, $matches)) {
            return $time;
        }

        $hour = (int) $matches[1];
        $minutes = $matches[2];
        $suffix = $hour < 12 ? 'a.m.' : 'p.m.';
        $hour12 = $hour % 12 === 0 ? 12 : $hour % 12;

        return sprintf('%d:%s %s', $hour12, $minutes, $suffix);
    }
}
