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

        <section class="patch-plan" id="parches" aria-labelledby="patch-plan-title">
            <div class="patch-plan-heading">
                <div>
                    <p class="public-kicker">Proceso técnico / Laravel 12</p>
                    <h2 id="patch-plan-title">Plan de gestión de parches</h2>
                    <p class="patch-plan-intro">Procedimiento para revisar dependencias, corregir vulnerabilidades y comprobar la aplicación antes y después de cada cambio.</p>
                </div>
                <a class="patch-plan-version" href="#manual-parches">Abrir procedimiento ↓</a>
            </div>

            <ol class="patch-stages">
                <li class="patch-stage">
                    <span class="patch-stage-number">01</span>
                    <span class="patch-stage-icon" aria-hidden="true">≡</span>
                    <h3>Inventario</h3>
                    <p>Identifica componentes, versiones y dependencias del proyecto.</p>
                    <code>composer show</code>
                </li>
                <li class="patch-stage">
                    <span class="patch-stage-number">02</span>
                    <span class="patch-stage-icon" aria-hidden="true">⌕</span>
                    <h3>Vulnerabilidades</h3>
                    <p>Revisa avisos de seguridad en las dependencias instaladas.</p>
                    <code>composer audit</code>
                </li>
                <li class="patch-stage">
                    <span class="patch-stage-number">03</span>
                    <span class="patch-stage-icon" aria-hidden="true">↓</span>
                    <h3>Aplicar parche</h3>
                    <p>Actualiza solo el paquete afectado y revisa el cambio antes de desplegar.</p>
                    <code>composer update proveedor/paquete</code>
                </li>
                <li class="patch-stage">
                    <span class="patch-stage-number">04</span>
                    <span class="patch-stage-icon" aria-hidden="true">✓</span>
                    <h3>Pruebas</h3>
                    <p>Ejecuta las pruebas automatizadas después de la actualización.</p>
                    <code>php artisan test</code>
                </li>
                <li class="patch-stage">
                    <span class="patch-stage-number">05</span>
                    <span class="patch-stage-icon" aria-hidden="true">▥</span>
                    <h3>Monitoreo</h3>
                    <p>Revisa los registros de Laravel después de publicar el cambio.</p>
                    <code>Get-Content storage/logs/laravel.log -Wait</code>
                </li>
            </ol>

        </section>

        <section class="patch-manual" id="manual-parches" aria-labelledby="patch-manual-title">
            <div class="patch-manual-heading">
                <p class="public-kicker">Manual de operación / Seguridad de aplicaciones</p>
                <h2 id="patch-manual-title">Gestión de parches y análisis con OWASP ZAP</h2>
                <p><strong>Objetivo:</strong> identificar vulnerabilidades, aplicar la corrección correspondiente y verificar que el sistema siga funcionando. Aplica este procedimiento en desarrollo o pruebas antes de publicar cambios.</p>
            </div>

            <div class="patch-glossary" aria-label="Glosario del manual">
                <article>
                    <h3>Parche</h3>
                    <p>Cambio puntual en el código o en una dependencia para corregir un fallo o cerrar una vulnerabilidad. Se prueba antes de publicarlo.</p>
                </article>
                <article>
                    <h3>OWASP ZAP</h3>
                    <p>Herramienta de análisis dinámico que inspecciona una aplicación web y reporta posibles problemas. No modifica el código ni instala actualizaciones.</p>
                </article>
                <article>
                    <h3>Composer</h3>
                    <p>Administrador de dependencias PHP. <code>composer audit</code> compara las versiones instaladas con avisos de seguridad conocidos.</p>
                </article>
                <article>
                    <h3>Hallazgo</h3>
                    <p>Resultado señalado por una auditoría o un escáner. Revisa su evidencia y confirma que sea reproducible antes de decidir la corrección.</p>
                </article>
            </div>

            <div class="patch-manual-workflow">
                <div class="patch-manual-steps">
                    <h3>Procedimiento</h3>
                    <ol>
                        <li><strong>Preparar el cambio.</strong> Trabaja en una rama y verifica el estado del repositorio con <code>git status</code>. Asegúrate de tener una copia recuperable antes de modificar dependencias.</li>
                        <li><strong>Auditar dependencias.</strong> En la carpeta raíz, ejecuta <code>composer audit</code>. Registra el paquete afectado, el aviso y la versión corregida recomendada.</li>
                        <li><strong>Analizar la aplicación.</strong> En OWASP ZAP, abre Quick Start, selecciona Automated Scan e introduce la URL de desarrollo, por ejemplo <code>http://127.0.0.1:8000</code>. Revisa las alertas y su evidencia.</li>
                        <li><strong>Aplicar la corrección.</strong> Sustituye <code>proveedor/paquete</code> por el nombre indicado en la auditoría y ejecuta <code>composer update proveedor/paquete --with-dependencies</code>. Revisa los cambios de <code>composer.lock</code>.</li>
                        <li><strong>Validar.</strong> Ejecuta <code>php artisan test</code>, vuelve a ejecutar <code>composer audit</code> y repite el análisis de ZAP. No publiques si fallan las pruebas o siguen pendientes alertas críticas confirmadas.</li>
                        <li><strong>Publicar y supervisar.</strong> Sigue el proceso de despliegue del equipo. En Windows, consulta el registro de Laravel con <code>Get-Content storage/logs/laravel.log -Wait</code>.</li>
                    </ol>
                </div>
                <aside class="patch-manual-note">
                    <span>CONTROL DE SEGURIDAD</span>
                    <h3>Analiza solo sistemas autorizados</h3>
                    <p>Los análisis activos de ZAP envían solicitudes de prueba que pueden cambiar datos. Limita el análisis a desarrollo o pruebas, y obtén autorización antes de evaluar cualquier otro entorno.</p>
                    <p>ZAP informa posibles problemas; el equipo confirma cada alerta y aplica la corrección. Configura autenticación en ZAP si necesitas revisar páginas protegidas.</p>
                </aside>
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
