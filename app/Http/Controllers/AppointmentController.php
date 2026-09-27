<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // Mis Citas
    public function index(Request $request)
    {
        $appointments = $request->user()->appointments()
            ->with(['barber', 'service'])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        $upcoming = $appointments->filter(fn ($a) => $a->status === 'scheduled' && $a->startsAt()->isFuture());
        $past = $appointments->diff($upcoming);

        return view('appointments.index', compact('upcoming', 'past'));
    }

    public function create()
    {
        return view('appointments.create', [
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
            'barbers' => User::where('role', 'barber')->orderBy('name')->get(),
            'slots' => Appointment::timeSlots(),
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();

        // REGLA DE NEGOCIO: verificar disponibilidad del barbero antes de guardar
        $taken = Appointment::where('barber_id', $data['barber_id'])
            ->where('date', $data['date'])
            ->where('time', $data['time'])
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($taken) {
            return back()
                ->withErrors(['time' => 'El barbero ya tiene una cita en esa fecha y hora. Elige otro horario.'])
                ->withInput();
        }

        $request->user()->appointments()->create($data + ['status' => 'scheduled']);

        return redirect()->route('appointments.index')->with('ok', 'Cita reservada correctamente.');
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        if (! $appointment->canBeCancelled()) {
            return back()->withErrors(['cancel' => 'Solo puedes cancelar citas programadas con más de 2 horas de anticipación.']);
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('ok', 'Cita cancelada.');
    }
}
