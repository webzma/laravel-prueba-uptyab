@extends('layouts.app')

@section('content')
    <h2>Iniciar sesión</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        Correo: <input type="email" name="email" value="{{ old('email') }}"><br>
        Contraseña: <input type="password" name="password"><br>
        <input type="checkbox" name="remember"> Recordarme<br>
        <button type="submit">Entrar</button>
    </form>
@endsection
