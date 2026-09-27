<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', Rule::exists('services', 'id')->where('is_active', true)],
            'barber_id' => ['required', Rule::exists('users', 'id')->where('role', 'barber')],
            // La fecha debe ser hoy o posterior
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            // La hora debe estar dentro del horario laboral (09:00 - 19:00) en intervalos de 60 min
            'time' => ['required', 'date_format:H:i', Rule::in(Appointment::timeSlots())],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }
                // Si es para hoy, la hora no puede haber pasado ya
                $start = Carbon::parse($this->date.' '.$this->time);
                if ($start->isPast()) {
                    $validator->errors()->add('time', 'Esa hora ya pasó.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'date.after_or_equal' => 'La fecha debe ser hoy o una fecha futura.',
            'time.in' => 'La hora debe estar dentro del horario laboral (09:00 a 19:00).',
            'barber_id.exists' => 'El barbero seleccionado no es válido.',
            'service_id.exists' => 'El servicio seleccionado no es válido.',
        ];
    }
}
