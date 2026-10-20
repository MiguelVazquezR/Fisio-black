<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class DateLabel
{
    public static function long(CarbonInterface|string $date): string
    {
        return ucfirst(
            self::localized($date)->translatedFormat('l j \d\e F \d\e Y')
        );
    }

    public static function dayMonth(CarbonInterface|string $date): string
    {
        return ucfirst(self::localized($date)->translatedFormat('j \d\e F'));
    }

    public static function weekday(CarbonInterface|string $date): string
    {
        return ucfirst(self::localized($date)->isoFormat('ddd'));
    }

    public static function range(CarbonInterface|string $start, CarbonInterface|string $end): string
    {
        $from = self::localized($start);
        $to = self::localized($end);

        if ($from->isSameMonth($to) && $from->isSameYear($to)) {
            $label = $from->day.' – '.$to->translatedFormat('j \d\e F \d\e Y');
        } elseif ($from->isSameYear($to)) {
            $label = $from->translatedFormat('j \d\e F').' – '.$to->translatedFormat('j \d\e F \d\e Y');
        } else {
            $label = $from->translatedFormat('j \d\e F \d\e Y').' – '.$to->translatedFormat('j \d\e F \d\e Y');
        }

        return ucfirst($label);
    }

    private static function localized(CarbonInterface|string $date): CarbonInterface
    {
        $parsed = CarbonImmutable::parse($date);
        $localized = $parsed->locale('es');

        return is_string($localized) ? $parsed : $localized;
    }
}
