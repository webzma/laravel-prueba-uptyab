<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'barber_id', 'service_id', 'date', 'time', 'status'])]
class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    // Horario laboral: turnos de 60 minutos de 09:00 a 18:00 (se cierra a las 19:00)
    public const OPEN_HOUR = 9;
    public const CLOSE_HOUR = 19;

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
        ];
    }

    public static function timeSlots(): array
    {
        $slots = [];
        for ($h = self::OPEN_HOUR; $h < self::CLOSE_HOUR; $h++) {
            $slots[] = sprintf('%02d:00', $h);
        }

        return $slots;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function barber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'barber_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function startsAt(): Carbon
    {
        return Carbon::parse($this->date->format('Y-m-d').' '.$this->time);
    }

    // Solo se puede cancelar si está programada y faltan más de 2 horas
    public function canBeCancelled(): bool
    {
        return $this->status === 'scheduled'
            && now()->addHours(2)->lt($this->startsAt());
    }
}
