<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Portal Seguro, CMS institucional para organizar contenidos y evidencias.">
    <title>{{ $title ?? 'Portal Seguro | CMS institucional' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="public-site">
    <header class="public-header">
        <a class="public-brand" href="{{ route('home') }}">
            <span class="public-brand-mark">PS</span>
            <span><strong>Portal Seguro</strong><small>CMS institucional</small></span>
        </a>
        <nav class="public-nav" aria-label="Navegación principal">
            <a href="#inicio">Inicio</a>
            <a href="#servicios">Proyecto</a>
            <a href="#noticias">Avances</a>
            <a href="#contacto">Contacto</a>
        </nav>
        <div class="public-actions">
            @auth
                <a class="public-login" href="{{ route('dashboard') }}">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="public-logout" type="submit">Cerrar sesión</button>
                </form>
            @else
                <a class="public-login" href="{{ route('login') }}">Iniciar sesión</a>
                <a class="public-register" href="{{ route('register') }}">Crear cuenta</a>
            @endauth
        </div>
    </header>

    @if (session('status'))
        <div class="public-status" role="status">{{ session('status') }}</div>
    @endif

    @yield('content')

    <footer class="public-footer" id="contacto">
        <div><strong>Portal Seguro</strong><p>Planeación, contenidos y seguridad en un solo espacio.</p></div>
        <div><span>Contacto</span><a href="mailto:contacto@portal.test">contacto@portal.test</a><a href="tel:+576000000000">+57 600 000 0000</a></div>
        <div><span>Atención</span><p>Lunes a viernes<br>8:00 a. m. - 5:00 p. m.</p></div>
    </footer>
</body>
</html>
