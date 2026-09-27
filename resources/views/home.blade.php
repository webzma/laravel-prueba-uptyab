@extends('layouts.app')

@section('content')
    <h2>Bienvenido a la barbería</h2>
    <p>Horario: 09:00 a 19:00. Turnos de 60 minutos.</p>

    <h3>Nuestros servicios</h3>
    <table border="1">
        <tr><th>Servicio</th><th>Descripción</th><th>Precio</th><th>Duración</th></tr>
        @foreach ($services as $service)
            <tr>
                <td>{{ $service->name }}</td>
                <td>{{ $service->description }}</td>
                <td>${{ $service->price }}</td>
                <td>{{ $service->duration }} min</td>
            </tr>
        @endforeach
    </table>

    <p><a href="{{ route('appointments.create') }}">Reservar una cita</a></p>
@endsection
