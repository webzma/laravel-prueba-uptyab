@extends('layouts.app')

@section('content')
    <h2>Registro</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        Nombre: <input type="text" name="name" value="{{ old('name') }}"><br>
        Correo: <input type="email" name="email" value="{{ old('email') }}"><br>
        Teléfono: <input type="text" name="phone" value="{{ old('phone') }}"><br>
        Contraseña: <input type="password" name="password"><br>
        Repetir contraseña: <input type="password" name="password_confirmation"><br>
        <button type="submit">Registrarme</button>
    </form>
@endsection
