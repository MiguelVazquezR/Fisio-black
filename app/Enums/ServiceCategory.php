<?php

namespace App\Enums;

enum ServiceCategory: string
{
    case Inyectables = 'inyectables';
    case Rehabilitacion = 'rehabilitacion';
    case Especializados = 'especializados';
    case Ortopedia = 'ortopedia';

    public function label(): string
    {
        return match ($this) {
            self::Inyectables => 'Inyectables',
            self::Rehabilitacion => 'Rehabilitación',
            self::Especializados => 'Especializados',
            self::Ortopedia => 'Ortopedia',
        };
    }
}
