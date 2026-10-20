<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Support\TimeLabel;
use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $patient_name
 * @property string $patient_phone
 * @property string|null $patient_email
 * @property int $service_id
 * @property bool $is_home_service
 * @property string|null $address_notes
 * @property Carbon $appointment_date
 * @property string $appointment_time
 * @property AppointmentStatus $status
 * @property string|null $notes
 * @property-read Service $service
 */
#[Fillable([
    'patient_name',
    'patient_phone',
    'patient_email',
    'service_id',
    'is_home_service',
    'address_notes',
    'appointment_date',
    'appointment_time',
    'status',
    'notes',
])]
class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'appointment_date' => 'date:Y-m-d',
            'is_home_service' => 'boolean',
            'status' => AppointmentStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function timeLabel(): string
    {
        return TimeLabel::twelveHour($this->appointment_time);
    }

    public function folio(): string
    {
        return 'FB-'.str_pad((string) $this->getKey(), 4, '0', STR_PAD_LEFT);
    }

    public function startsAt(): CarbonImmutable
    {
        return CarbonImmutable::parse(
            $this->appointment_date->toDateString().' '.$this->appointment_time
        );
    }

    public function addressLabel(): string
    {
        return $this->is_home_service && filled($this->address_notes)
            ? (string) $this->address_notes
            : (string) config('fisio.clinic.address');
    }

    public function modalityLabel(): string
    {
        return $this->is_home_service ? 'A domicilio' : 'En clínica';
    }

    /**
     * @param  Builder<Appointment>  $query
     * @return Builder<Appointment>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', AppointmentStatus::activeValues());
    }

    /**
     * @param  Builder<Appointment>  $query
     * @return Builder<Appointment>
     */
    public function scopeOnDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('appointment_date', $date);
    }
}
