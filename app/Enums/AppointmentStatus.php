<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Rescheduled = 'rescheduled';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Confirmed => 'Confirmada',
            self::Rescheduled => 'Reagendada',
            self::Cancelled => 'Cancelada',
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::Pending, self::Confirmed, self::Rescheduled => true,
            self::Cancelled => $status === self::Cancelled,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function activeValues(): array
    {
        return [self::Pending->value, self::Confirmed->value, self::Rescheduled->value];
    }
}
