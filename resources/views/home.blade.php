@extends('layouts.app')

@section('content')
    <main id="inicio">
        <section class="public-hero">
            <div class="public-hero-copy">
                <p class="public-kicker">Portal Seguro / CMS institucional</p>
                <h1>Del plan al avance real.</h1>
                <p class="public-hero-text">Un espacio para organizar contenidos, proteger la información y demostrar cómo crece el proyecto del equipo.</p>
                <div class="public-hero-actions">
                    @auth
                        <a class="public-primary" href="{{ route('dashboard') }}">Entrar al CMS <span>→</span></a>
                    @else
                        <a class="public-primary" href="{{ route('login') }}">Entrar al CMS <span>→</span></a>
                    @endauth
                    <a class="public-secondary" href="#servicios">Conocer el proyecto</a>
                </div>
            </div>
            <div class="public-hero-panel" aria-label="Resumen institucional">
                <div class="hero-panel-top"><span>01</span><span>Estamos aquí</span></div>
                <div class="hero-panel-line"></div>
                <strong>Planeamos.<br>Construimos.</strong>
                <p>El trabajo se organiza en historias, responsables, controles y evidencias.</p>
                <span class="hero-panel-arrow">↗</span>
            </div>
        </section>

        <section class="public-section" id="servicios">
            <div class="public-section-heading"><div><p class="public-kicker">Mapa del CMS</p><h2>¿Qué estamos construyendo?</h2></div><span class="public-index">01 / 04</span></div>
            <div class="public-service-grid">
                <article class="public-service-card"><span>01</span><h3>Contenidos</h3><p>Paginas, noticias y servicios gestionados desde un solo lugar.</p><a href="#contacto">Ver alcance <b>→</b></a></article>
                <article class="public-service-card featured"><span>02</span><h3>Equipo</h3><p>Responsabilidades claras para backend, frontend, base de datos, QA y seguridad.</p><a href="#contacto">Conocer roles <b>→</b></a></article>
                <article class="public-service-card"><span>03</span><h3>Seguridad</h3><p>Autenticacion, sesiones, validaciones y evidencias que reducen riesgos.</p><a href="#noticias">Ver controles <b>→</b></a></article>
            </div>
        </section>

        <section class="public-news" id="noticias">
            <div class="public-section-heading"><div><p class="public-kicker">Estado del proyecto</p><h2>Avances y siguientes pasos</h2></div><a class="public-text-link" href="#contacto">Conocer más →</a></div>
            <div class="public-news-grid">
                <article class="public-news-card"><span>SPRINT 01 / ACTIVO</span><h3>Login, registro y dashboard ya tienen una base segura.</h3><p>El equipo puede demostrar autenticacion, sesiones regeneradas, CSRF y validaciones.</p><a href="{{ route('login') }}">Probar acceso →</a></article>
                <article class="public-news-card dark"><span>PROXIMO INCREMENTO</span><h3>Roles, contenidos y evidencias serán el siguiente foco.</h3><p>La planeacion conecta cada historia con su responsable, prueba y commit.</p><a href="#contacto">Ver proxima etapa →</a></article>
            </div>
        </section>
    </main>
@endsection
