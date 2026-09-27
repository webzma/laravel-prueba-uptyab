@extends('layouts.app')

@section('content')
    <h2>Servicios</h2>
    <p><a href="{{ route('admin.services.create') }}">+ Nuevo servicio</a></p>

    <table border="1">
        <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Duración</th><th>Activo</th><th>Acciones</th></tr>
        @foreach ($services as $service)
            <tr>
                <td>{{ $service->id }}</td>
                <td>{{ $service->name }}</td>
                <td>{{ $service->description }}</td>
                <td>${{ $service->price }}</td>
                <td>{{ $service->duration }} min</td>
                <td>{{ $service->is_active ? 'SI' : 'NO' }}</td>
                <td>
                    <a href="{{ route('admin.services.edit', $service) }}">Editar</a>
                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit">{{ $service->is_active ? 'Desactivar' : 'Activar' }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
