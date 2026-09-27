@extends('layouts.app')

@section('content')
    <h2>{{ $service->exists ? 'Editar servicio' : 'Nuevo servicio' }}</h2>

    <form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}">
        @csrf
        @if ($service->exists)
            @method('PUT')
        @endif
        Nombre: <input type="text" name="name" value="{{ old('name', $service->name) }}"><br>
        Descripción: <textarea name="description">{{ old('description', $service->description) }}</textarea><br>
        Precio: <input type="number" step="0.01" name="price" value="{{ old('price', $service->price) }}"><br>
        Duración (min): <input type="number" name="duration" value="{{ old('duration', $service->duration) }}"><br>
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active))> Activo<br>
        <button type="submit">Guardar</button>
        <a href="{{ route('admin.services.index') }}">Volver</a>
    </form>
@endsection
