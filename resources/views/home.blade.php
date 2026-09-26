@extends('layouts.app')

@section('content')
    <main id="inicio">
        <section class="public-hero">
            <div class="public-hero-copy">
                <div class="public-badges">
                    <span class="public-badge">CMS institucional</span>
                    <span class="public-badge muted">Seguridad + trazabilidad</span>
                </div>
                <p class="public-kicker">Proyecto institucional / estado actual</p>
                <h1>Un CMS institucional, construido con seguridad.</h1>
                <p class="public-hero-text">Portal Seguro reúne una página pública, registro e inicio de sesión, un dashboard protegido y módulos preparados para ampliar la gestión institucional.</p>
                <div class="public-hero-actions">
                    @auth
                        <a class="public-primary" href="{{ route('dashboard') }}">Entrar al CMS <span>→</span></a>
                    @else
                        <a class="public-primary" href="{{ route('login') }}">Entrar al CMS <span>→</span></a>
                    @endauth
                    <a class="public-secondary" href="#servicios">Conocer el proyecto</a>
                </div>
                <ul class="public-proof-list" aria-label="Indicadores del portal">
                    <li>Autenticación segura</li>
                    <li>Seguimiento operativo</li>
                    <li>Contenido controlado</li>
                </ul>
            </div>
            <div class="public-hero-panel" aria-label="Resumen institucional">
                <div class="hero-panel-top"><span>BASE FUNCIONAL</span><span>En desarrollo</span></div>
                <div class="hero-panel-line"></div>
                <strong>Organizamos.<br>Protegemos.</strong>
                <p>La estructura incluye acceso autenticado y seis módulos. Las funciones de gestión de cada módulo están previstas para etapas posteriores.</p>
                <div class="hero-metrics" aria-label="Métricas del portal">
                    <div>
                        <strong>06</strong>
                        <span>módulos</span>
                    </div>
                    <div>
                        <strong>03</strong>
                        <span>flujos base</span>
                    </div>
                </div>
                <span class="hero-panel-arrow">↗</span>
            </div>
        </section>

        <section class="public-section" id="servicios">
            <div class="public-section-heading"><div><p class="public-kicker">Alcance actual</p><h2>La base del CMS</h2></div><span class="public-index">01 / 03</span></div>
            <div class="public-service-grid">
                <article class="public-service-card"><span>01</span><h3>Acceso</h3><p>Registro e inicio de sesión con validación de datos y acceso autenticado al dashboard.</p><a href="#noticias">Ver estado <b>→</b></a></article>
                <article class="public-service-card featured"><span>02</span><h3>Seguridad base</h3><p>Sesiones protegidas, hash de contraseñas y protección CSRF en los formularios.</p><a href="#noticias">Ver controles <b>→</b></a></article>
                <article class="public-service-card"><span>03</span><h3>Módulos</h3><p>Secciones de contenidos, usuarios, multimedia, planeación, seguridad y evidencias.</p><a href="#noticias">Ver siguiente etapa <b>→</b></a></article>
            </div>
        </section>

        <section class="public-news" id="noticias">
            <div class="public-section-heading"><div><p class="public-kicker">Estado del proyecto</p><h2>Disponible y por completar</h2></div><span class="public-index">02 / 03</span></div>
            <div class="public-news-grid">
                <article class="public-news-card"><span>IMPLEMENTADO</span><h3>La estructura principal y el acceso ya están disponibles.</h3><p>La aplicación incluye página pública, registro, inicio de sesión y dashboard protegido para usuarios autenticados.</p><a href="{{ route('login') }}">Ir al acceso →</a></article>
                <article class="public-news-card dark"><span>POR COMPLETAR</span><h3>Los módulos necesitan sus funciones de gestión.</h3><p>La siguiente etapa es implementar operaciones de contenido y roles, completar pruebas y registrar evidencias de cada entrega.</p><a href="{{ route('register') }}">Crear cuenta →</a></article>
            </div>
        </section>
    </main>
@endsection
