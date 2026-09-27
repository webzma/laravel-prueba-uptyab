<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    // Agenda diaria (por defecto el día actual)
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        $query = Appointment::with(['client', 'barber', 'service'])
            ->where('date', $date)
            ->orderBy('time');

        // El barbero solo ve sus citas; el admin ve todas
        if ($request->user()->isBarber()) {
            $query->where('barber_id', $request->user()->id);
        }

        return view('admin.agenda', [
            'appointments' => $query->get(),
            'date' => $date,
        ]);
    }

    public function complete(Request $request, Appointment $appointment)
    {
        if ($request->user()->isBarber()) {
            abort_unless($appointment->barber_id === $request->user()->id, 403);
        }

        $appointment->update(['status' => 'completed']);

        return back()->with('ok', 'Cita marcada como completada.');
    }
}
