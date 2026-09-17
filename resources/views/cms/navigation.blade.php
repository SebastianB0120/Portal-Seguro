<a class="dashboard-brand" href="{{ route('dashboard') }}">
    <span class="brand-mark">PS</span>
    <span><strong>Portal Seguro</strong><small>CMS de trabajo</small></span>
</a>

<p class="dashboard-label">Espacio de trabajo</p>
<nav class="dashboard-nav" aria-label="Menu del CMS">
    <a class="{{ request()->routeIs('dashboard') ? 'is-current' : '' }}" href="{{ route('dashboard') }}"><span>+</span> Resumen</a>
    <a class="{{ request()->route('module') === 'contenidos' ? 'is-current' : '' }}" href="{{ route('cms.module', 'contenidos') }}"><span>#</span> Contenidos <small>E4</small></a>
    <a class="{{ request()->route('module') === 'usuarios' ? 'is-current' : '' }}" href="{{ route('cms.module', 'usuarios') }}"><span>@</span> Usuarios y roles <small>E2</small></a>
    <a class="{{ request()->route('module') === 'multimedia' ? 'is-current' : '' }}" href="{{ route('cms.module', 'multimedia') }}"><span>[]</span> Multimedia <small>E6</small></a>
</nav>

<p class="dashboard-label dashboard-label-secondary">Control del proyecto</p>
<nav class="dashboard-nav" aria-label="Menu de seguimiento">
    <a class="{{ request()->route('module') === 'planeacion' ? 'is-current' : '' }}" href="{{ route('cms.module', 'planeacion') }}"><span>~</span> Planeacion <small>E1</small></a>
    <a class="{{ request()->route('module') === 'seguridad' ? 'is-current' : '' }}" href="{{ route('cms.module', 'seguridad') }}"><span>!</span> Seguridad <small>E10</small></a>
    <a class="{{ request()->route('module') === 'evidencias' ? 'is-current' : '' }}" href="{{ route('cms.module', 'evidencias') }}"><span>+</span> Evidencias <small>QA</small></a>
</nav>

<div class="dashboard-sidebar-note">
    <span class="online-dot"></span>
    <div><strong>Entorno protegido</strong><small>Sesion autenticada</small></div>
</div>
