@extends('layouts.app')

@section('content')
    <h2>Reservar cita</h2>
    <form method="POST" action="{{ route('appointments.store') }}">
        @csrf
        1. Servicio:
        <select name="service_id">
            <option value="">-- elegir --</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>
                    {{ $service->name }} (${{ $service->price }} - {{ $service->duration }} min)
                </option>
            @endforeach
        </select><br>

        2. Barbero:
        <select name="barber_id">
            <option value="">-- elegir --</option>
            @foreach ($barbers as $barber)
                <option value="{{ $barber->id }}" @selected(old('barber_id') == $barber->id)>{{ $barber->name }}</option>
            @endforeach
        </select><br>

        3. Fecha: <input type="date" name="date" value="{{ old('date') }}" min="{{ now()->format('Y-m-d') }}"><br>

        Hora:
        <select name="time">
            <option value="">-- elegir --</option>
            @foreach ($slots as $slot)
                <option value="{{ $slot }}" @selected(old('time') == $slot)>{{ $slot }}</option>
            @endforeach
        </select><br>

        <button type="submit">Reservar</button>
    </form>
@endsection
