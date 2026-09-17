<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $module['title'] }} | Portal Seguro</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="secure-dashboard">
    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            @include('cms.navigation')
        </aside>
        <main class="dashboard-main module-main">
            <header class="dashboard-topbar">
                <div><p class="dashboard-kicker">{{ $module['label'] }}</p><h1>{{ $module['title'] }}</h1></div>
                <div class="dashboard-account"><div class="dashboard-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button" type="submit">Salir</button></form></div>
            </header>
            <section class="module-hero"><div><p class="dashboard-kicker">Área de trabajo</p><h2>{{ $module['description'] }}</h2><button class="module-action" type="button">{{ $module['action'] }} <span>+</span></button></div><div class="module-number">01</div></section>
            <section class="module-list"><div class="panel-heading"><div><p class="dashboard-kicker">Contenido</p><h3>Opciones de esta sección</h3></div><span class="secure-badge">Acceso seguro</span></div>@foreach ($module['items'] as $index => $item)<div class="module-list-item"><span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $item }}</strong><small>Disponible próximamente</small><b>→</b></div>@endforeach</section>
        </main>
    </div>
</body>
</html>