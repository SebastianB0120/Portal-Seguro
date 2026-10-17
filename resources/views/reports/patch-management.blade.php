@extends('layouts.app', ['title' => 'Informe de parches y OWASP ZAP | Portal Seguro'])

@section('content')
    <main class="patch-report-page">
        <header class="report-heading">
            <div>
                <p class="public-kicker">Informe técnico / Seguridad de aplicaciones</p>
                <h1>Gestión de parches y OWASP ZAP</h1>
                <p>Informe generado a partir del código del proyecto y del análisis de OWASP ZAP.</p>
            </div>
            <div class="report-actions" aria-label="Acciones del informe">
                <a class="report-back-link" href="{{ route('home') }}#manual-parches">Volver al manual</a>
                <button class="report-button" type="button" id="capture-report">Volver a capturar</button>
                <button class="report-button primary" type="button" id="print-report">Imprimir / Guardar PDF</button>
            </div>
        </header>

        <p class="report-status" id="zap-status-message" role="status" aria-live="polite">Conectando con OWASP ZAP para iniciar el análisis pasivo...</p>
        <p class="report-status" id="capture-status" role="status" aria-live="polite">Preparando capturas del informe...</p>

        <section class="report-section report-overview report-capture" data-report-capture="Resumen" id="report-overview" aria-labelledby="report-overview-title">
            <div class="report-section-heading">
                <div>
                    <p class="public-kicker">01 / Alcance</p>
                    <h2 id="report-overview-title">Resumen de la revisión</h2>
                </div>
                <span class="report-stamp">{{ $report['createdAt'] }}</span>
            </div>
            <p>El informe registra el análisis pasivo ejecutado por OWASP ZAP, junto con el procedimiento de revisión de dependencias y pruebas de Laravel.</p>
            <dl class="report-facts">
                <div><dt>PHP</dt><dd>{{ $report['phpVersion'] }}</dd></div>
                <div><dt>Laravel</dt><dd>{{ $report['laravelVersion'] }}</dd></div>
                <div><dt>Objetivo configurado</dt><dd>{{ $report['targetUrl'] }}</dd></div>
                <div><dt>Estado de ZAP</dt><dd>{{ $report['zapStatus'] }}</dd></div>
            </dl>
            <p class="report-caveat" id="zap-result-message">{{ $report['zapResult']['message'] ?? 'El análisis se iniciará automáticamente al abrir este informe.' }}</p>
        </section>

        <section class="report-section report-capture" data-report-capture="Código" id="report-implementation" aria-labelledby="report-implementation-title">
            <div class="report-section-heading">
                <div>
                    <p class="public-kicker">02 / Implementación</p>
                    <h2 id="report-implementation-title">Archivos que implementan el manual</h2>
                </div>
            </div>
            <p>La vista contiene las fases del plan, el glosario y el procedimiento. La ruta siguiente entrega este informe; la prueba de funcionalidad comprueba los contenidos principales.</p>
            <dl class="report-file-list">
                @foreach ($report['files'] as $purpose => $path)
                    <div><dt>{{ $purpose }}</dt><dd><code>{{ $path }}</code></dd></div>
                @endforeach
            </dl>
            <h3>Comandos del procedimiento</h3>
            <div class="report-command-list">
                @foreach ($report['commands'] as $item)
                    <div><span>{{ $item['label'] }}</span><code>{{ $item['command'] }}</code></div>
                @endforeach
            </div>
            <h3>Extracto real del manual Blade</h3>
            <p>Este fragmento se lee desde <code>resources/views/home.blade.php</code> al generar el informe.</p>
            <pre class="report-source-code"><code>{{ $report['manualSource'] }}</code></pre>
            <h3>Integración automática con la API de OWASP ZAP</h3>
            <p>El siguiente código corresponde al controlador que inicia el spider, espera el análisis pasivo y recoge las alertas.</p>
            <pre class="report-source-code"><code>{{ $report['zapSource'] }}</code></pre>
        </section>

        <section class="report-section report-zap report-capture" data-report-capture="OWASP-ZAP" id="report-zap" aria-labelledby="report-zap-title" data-zap-scan-url="{{ route('reports.patches.zap-scan') }}" data-csrf-token="{{ csrf_token() }}">
            <div class="report-section-heading">
                <div>
                    <p class="public-kicker">03 / Análisis dinámico</p>
                    <h2 id="report-zap-title">Registro de OWASP ZAP</h2>
                </div>
                <span class="report-status-badge">{{ $report['zapStatus'] }}</span>
            </div>
            <button class="report-button" type="button" id="run-zap-scan">Ejecutar análisis pasivo</button>
            <div class="report-zap-results" id="zap-results" aria-live="polite">
                @if (is_array($report['zapResult'] ?? null) && isset($report['zapResult']['counts']))
                    <p>Versión ZAP: {{ $report['zapResult']['zapVersion'] ?? 'No disponible' }}. Fecha: {{ $report['zapResult']['scannedAt'] ?? 'No disponible' }}.</p>
                    <dl class="report-facts" id="zap-counts">
                        @foreach (($report['zapResult']['counts'] ?? []) as $risk => $count)
                            <div><dt>{{ $risk }}</dt><dd>{{ $count }}</dd></div>
                        @endforeach
                    </dl>
                    <ol class="report-zap-alerts" id="zap-alert-list">
                        @foreach (($report['zapResult']['alerts'] ?? []) as $alert)
                            <li><strong>{{ $alert['risk'] ?? 'Informational' }}</strong> {{ $alert['alert'] ?? 'Alerta sin título' }} <code>{{ $alert['url'] ?? '' }}</code></li>
                        @endforeach
                    </ol>
                @else
                    <p>Los resultados de ZAP aparecerán aquí cuando termine el análisis.</p>
                    <dl class="report-facts" id="zap-counts"></dl>
                    <ol class="report-zap-alerts" id="zap-alert-list"></ol>
                @endif
            </div>
            <ol class="report-zap-steps">
                <li>El informe solicita a la API local de ZAP que rastree el sitio configurado: <code>{{ $report['targetUrl'] }}</code>.</li>
                <li>Espera a que termine el spider y la cola del análisis pasivo.</li>
                <li>Las alertas y su nivel de riesgo se muestran arriba y se guardan en el informe durante 24 horas.</li>
            </ol>
            <p class="report-caveat">Este flujo no ejecuta un análisis activo ni aplica parches. Las alertas pasivas son indicios que el equipo debe revisar. El objetivo está limitado a localhost por seguridad.</p>
        </section>

        <section class="report-section report-captures" id="report-captures" aria-labelledby="report-captures-title">
            <div class="report-section-heading">
                <div>
                    <p class="public-kicker">04 / Evidencias automáticas</p>
                    <h2 id="report-captures-title">Capturas del informe</h2>
                </div>
            </div>
            <p class="capture-empty" id="capture-empty">Al cargar la página se generan capturas PNG del resumen, el código y el procedimiento. Las capturas aparecen aquí con su enlace de descarga.</p>
            <div class="capture-gallery" id="capture-gallery" aria-live="polite"></div>
        </section>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" defer></script>
    <script src="{{ asset('js/patch-report.js') }}" defer></script>
@endsection
