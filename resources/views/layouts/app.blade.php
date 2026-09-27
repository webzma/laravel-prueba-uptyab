<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BarberShop App</title>
</head>
<body>
    <h1>BarberShop App</h1>

    <p>
        <a href="{{ route('home') }}">Inicio</a> |
        @auth
            <a href="{{ route('appointments.create') }}">Reservar cita</a> |
            <a href="{{ route('appointments.index') }}">Mis citas</a> |
            @if (in_array(auth()->user()->role, ['barber', 'admin']))
                <a href="{{ route('admin.agenda') }}">Agenda</a> |
                <a href="{{ route('admin.services.index') }}">Servicios</a> |
            @endif
            Hola {{ auth()->user()->name }} ({{ auth()->user()->role }})
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit">Salir</button>
            </form>
        @else
            <a href="{{ route('login') }}">Entrar</a> |
            <a href="{{ route('register') }}">Registrarse</a>
        @endauth
    </p>
    <hr>

    @if (session('ok'))
        <p><b>{{ session('ok') }}</b></p>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li><font color="red">{{ $error }}</font></li>
            @endforeach
        </ul>
    @endif

    @yield('content')
</body>
</html>
