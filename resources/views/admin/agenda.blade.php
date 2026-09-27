@extends('layouts.app')

@section('content')
    <h2>Agenda del día {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h2>

    <form method="GET" action="{{ route('admin.agenda') }}">
        Ver otro día: <input type="date" name="date" value="{{ $date }}">
        <button type="submit">Ver</button>
    </form>
    <br>

    @if ($appointments->isEmpty())
        <p>No hay citas para este día.</p>
    @else
        <table border="1">
            <tr>
                <th>Hora</th><th>Cliente</th><th>Teléfono</th><th>Servicio</th>
                @if (auth()->user()->isAdmin())<th>Barbero</th>@endif
                <th>Estado</th><th></th>
            </tr>
            @foreach ($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->time }}</td>
                    <td>{{ $appointment->client->name }}</td>
                    <td>{{ $appointment->client->phone }}</td>
                    <td>{{ $appointment->service->name }}</td>
                    @if (auth()->user()->isAdmin())<td>{{ $appointment->barber->name }}</td>@endif
                    <td>{{ $appointment->status }}</td>
                    <td>
                        @if ($appointment->status === 'scheduled')
                            <form method="POST" action="{{ route('admin.appointments.complete', $appointment) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit">Completada</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection
