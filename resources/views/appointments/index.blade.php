@extends('layouts.app')

@section('content')
    <h2>Mis citas</h2>

    <h3>Pendientes</h3>
    @if ($upcoming->isEmpty())
        <p>No tienes citas pendientes.</p>
    @else
        <table border="1">
            <tr><th>Fecha</th><th>Hora</th><th>Servicio</th><th>Barbero</th><th>Estado</th><th></th></tr>
            @foreach ($upcoming->sortBy(fn ($a) => $a->startsAt()) as $appointment)
                <tr>
                    <td>{{ $appointment->date->format('d/m/Y') }}</td>
                    <td>{{ $appointment->time }}</td>
                    <td>{{ $appointment->service->name }}</td>
                    <td>{{ $appointment->barber->name }}</td>
                    <td>{{ $appointment->status }}</td>
                    <td>
                        @if ($appointment->canBeCancelled())
                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" onclick="return confirm('¿Cancelar la cita?')">Cancelar</button>
                            </form>
                        @else
                            (no se puede cancelar, faltan menos de 2 horas)
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    <h3>Historial</h3>
    @if ($past->isEmpty())
        <p>No hay citas en el historial.</p>
    @else
        <table border="1">
            <tr><th>Fecha</th><th>Hora</th><th>Servicio</th><th>Barbero</th><th>Estado</th></tr>
            @foreach ($past as $appointment)
                <tr>
                    <td>{{ $appointment->date->format('d/m/Y') }}</td>
                    <td>{{ $appointment->time }}</td>
                    <td>{{ $appointment->service->name }}</td>
                    <td>{{ $appointment->barber->name }}</td>
                    <td>{{ $appointment->status }}</td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection
