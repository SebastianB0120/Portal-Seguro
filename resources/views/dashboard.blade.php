<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Panel autenticado de Portal Seguro">
    <title>Resumen del CMS | Portal Seguro</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="secure-dashboard">
    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            @include('cms.navigation')
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-topbar">
                <div><p class="dashboard-kicker">Workspace / Resumen</p><h1>Centro de control</h1></div>
                <div class="dashboard-account">
                    <div class="dashboard-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-button" type="submit">Salir</button>
                    </form>
                </div>
            </header>

            <section class="dashboard-welcome">
                <div><p class="dashboard-kicker">Sprint 01 / Avance actual</p><h2>El CMS toma forma, {{ auth()->user()->name }}.</h2><p>Un resumen claro para saber qué está construido, qué sigue y cómo comprobamos cada avance.</p><div class="welcome-meta"><span><b>Estado</b> En desarrollo</span><span><b>Última revisión</b> 26 sep 2026</span></div></div>
                <span class="welcome-shield">84%</span>
            </section>

            <section class="dashboard-stats" aria-label="Resumen del sistema">
                <article><span class="stat-icon green">@</span><div><small>Usuarios</small><strong>01</strong></div><em>Base lista</em></article>
                <article><span class="stat-icon yellow">#</span><div><small>Historias</small><strong>15</strong></div><em>Backlog inicial</em></article>
                <article><span class="stat-icon blue">!</span><div><small>Controles</small><strong>03</strong></div><em>Implementados</em></article>
                <article><span class="stat-icon coral">~</span><div><small>Sprint</small><strong>01</strong></div><em>En curso</em></article>
            </section>

            <section class="dashboard-columns">
                <article class="dashboard-panel"><div class="panel-heading"><div><p class="dashboard-kicker">Controles</p><h3>Seguridad incorporada</h3></div><span class="secure-badge">Activo</span></div><ul class="security-list"><li><span>+</span><div><strong>Autenticacion y hash</strong><small>Credenciales validadas y almacenadas con Hash::make</small></div></li><li><span>+</span><div><strong>Sesiones protegidas</strong><small>Regeneracion al entrar e invalidacion al salir</small></div></li><li><span>+</span><div><strong>CSRF y validaciones</strong><small>Formularios y entradas controlados por Laravel</small></div></li><li><span class="pending">!</span><div><strong>Roles y permisos</strong><small>Historia priorizada para el siguiente sprint</small></div></li></ul></article>
                <article class="dashboard-panel roadmap-panel"><div class="panel-heading"><div><p class="dashboard-kicker">Trazabilidad</p><h3>Del backlog al codigo</h3></div><span class="roadmap-count">03</span></div><div class="roadmap-item"><span>CMS-01</span><div><strong>Registro seguro</strong><small>RegisterController + validacion</small></div><b>+</b></div><div class="roadmap-item"><span>CMS-02</span><div><strong>Dashboard protegido</strong><small>Ruta auth + vista de resumen</small></div><b>+</b></div><div class="roadmap-item"><span>CMS-03</span><div><strong>Menu del CMS</strong><small>Areas y estados de trabajo</small></div><b>+</b></div></article>
            </section>

            <section class="dashboard-next-step">
                <div><p class="dashboard-kicker">Siguiente movimiento</p><h3>Convertir la planeacion en evidencia</h3><p>Documenta cada historia con responsable, criterio de aceptacion, control de seguridad y referencia al commit.</p></div>
                <a class="dashboard-cta" href="{{ route('cms.module', 'planeacion') }}">Abrir planeacion <span>+</span></a>
            </section>
        </main>
    </div>
</body>
</html>