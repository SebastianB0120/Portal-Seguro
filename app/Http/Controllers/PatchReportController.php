<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class PatchReportController extends Controller
{
    public function show(): View
    {
        $homeView = file_get_contents(resource_path('views/home.blade.php')) ?: '';
        $zapSource = file_get_contents(__FILE__) ?: '';
        preg_match('/<section class="patch-manual"[\s\S]*?<\/section>/', $homeView, $manualMatch);
        $zapResult = Cache::get('reports.zap.latest');

        return view('reports.patch-management', [
            'report' => [
                'createdAt' => now()->format('d/m/Y H:i:s'),
                'phpVersion' => PHP_VERSION,
                'laravelVersion' => app()->version(),
                'targetUrl' => (string) config('zap.target_url'),
                'zapStatus' => $zapResult['status'] ?? 'Pendiente de análisis automático',
                'zapResult' => $zapResult,
                'manualSource' => $manualMatch[0] ?? 'No se encontró el bloque del manual en la vista de inicio.',
                'zapSource' => $zapSource,
                'files' => [
                    'Manual y flujo' => 'resources/views/home.blade.php',
                    'Integración de ZAP' => 'app/Http/Controllers/PatchReportController.php',
                    'Configuración de ZAP' => 'config/zap.php',
                    'Estilos del manual' => 'resources/css/app.css',
                    'Ruta del informe' => 'routes/web.php',
                    'Prueba automatizada' => 'tests/Feature/ExampleTest.php',
                ],
                'commands' => [
                    ['label' => 'Inventariar dependencias', 'command' => 'composer show'],
                    ['label' => 'Auditar vulnerabilidades conocidas', 'command' => 'composer audit'],
                    ['label' => 'Actualizar el paquete corregido', 'command' => 'composer update proveedor/paquete --with-dependencies'],
                    ['label' => 'Ejecutar pruebas Laravel', 'command' => 'php artisan test'],
                    ['label' => 'Revisar registros en PowerShell', 'command' => 'Get-Content storage/logs/laravel.log -Wait'],
                ],
            ],
        ]);
    }

    public function scan(): JsonResponse
    {
        $targetUrl = rtrim((string) config('zap.target_url'), '/');
        $localHosts = ['127.0.0.1', 'localhost', '::1'];
        $targetParts = parse_url($targetUrl);
        $targetHost = strtolower(trim((string) ($targetParts['host'] ?? ''), '[]'));

        if (!is_array($targetParts)
            || !in_array(strtolower((string) ($targetParts['scheme'] ?? '')), ['http', 'https'], true)
            || !in_array($targetHost, $localHosts, true)
            || isset($targetParts['user'])
            || isset($targetParts['pass'])) {
            return response()->json([
                'status' => 'Escaneo detenido',
                'message' => 'Por seguridad, el análisis automático solo permite objetivos locales: localhost, 127.0.0.1 o ::1.',
            ], 422);
        }

        $apiUrl = rtrim((string) config('zap.api_url'), '/');
        $apiParts = parse_url($apiUrl);
        $apiHost = strtolower(trim((string) (is_array($apiParts) ? ($apiParts['host'] ?? '') : ''), '[]'));
        if (!is_array($apiParts)
            || !in_array(strtolower((string) ($apiParts['scheme'] ?? '')), ['http', 'https'], true)
            || !in_array($apiHost, $localHosts, true)) {
            return $this->storeZapResult([
                'status' => 'Escaneo detenido',
                'message' => 'Por seguridad, la API de ZAP debe estar configurada en localhost, 127.0.0.1 o ::1.',
                'targetUrl' => $targetUrl,
                'alerts' => [],
                'counts' => [],
                'scannedAt' => now()->toDateTimeString(),
            ], 422);
        }

        $apiKey = (string) config('zap.api_key');
        $apiParameters = $apiKey === '' ? [] : ['apikey' => $apiKey];

        try {
            $versionResponse = Http::timeout(5)->get($apiUrl . '/JSON/core/view/version/', $apiParameters)->throw();
            $zapVersion = (string) $versionResponse->json('version', 'desconocida');

            $scanResponse = Http::timeout(10)->get($apiUrl . '/JSON/spider/action/scan/', array_merge($apiParameters, [
                'url' => $targetUrl,
                'maxChildren' => '10',
                'recurse' => 'true',
            ]))->throw();
            $scanId = $scanResponse->json('scan');

            if (!is_string($scanId) && !is_numeric($scanId)) {
                return response()->json([
                    'status' => 'Error de ZAP',
                    'message' => 'ZAP respondió, pero no devolvió un identificador de spider válido.',
                ], 502);
            }

            $spiderDeadline = microtime(true) + max(1, (int) config('zap.spider_timeout_seconds'));
            $spiderPercent = 0;
            do {
                usleep(500_000);
                $spiderResponse = Http::timeout(5)->get($apiUrl . '/JSON/spider/view/status/', array_merge($apiParameters, [
                    'scanId' => (string) $scanId,
                ]))->throw();
                $spiderPercent = (int) $spiderResponse->json('status', 0);
            } while ($spiderPercent < 100 && microtime(true) < $spiderDeadline);

            if ($spiderPercent < 100) {
                return $this->storeZapResult([
                    'status' => 'Spider incompleto',
                    'message' => 'El spider alcanzó el tiempo máximo configurado; no se obtuvieron resultados completos.',
                    'targetUrl' => $targetUrl,
                    'zapVersion' => $zapVersion,
                    'spiderPercent' => $spiderPercent,
                    'alerts' => [],
                    'counts' => [],
                    'scannedAt' => now()->toDateTimeString(),
                ], 504);
            }

            $passiveDeadline = microtime(true) + max(1, (int) config('zap.passive_scan_timeout_seconds'));
            $recordsRemaining = 0;
            do {
                $queueResponse = Http::timeout(5)->get($apiUrl . '/JSON/pscan/view/recordsToScan/', $apiParameters)->throw();
                $recordsRemaining = (int) $queueResponse->json('recordsToScan', 0);
                if ($recordsRemaining > 0) {
                    usleep(500_000);
                }
            } while ($recordsRemaining > 0 && microtime(true) < $passiveDeadline);

            $alertsResponse = Http::timeout(10)->get($apiUrl . '/JSON/alert/view/alerts/', array_merge($apiParameters, [
                'baseurl' => $targetUrl,
                'start' => '0',
                'count' => '500',
            ]))->throw();
            $alerts = $alertsResponse->json('alerts', []);
            $alerts = is_array($alerts) ? array_values(array_filter($alerts, 'is_array')) : [];
            $counts = ['High' => 0, 'Medium' => 0, 'Low' => 0, 'Informational' => 0];

            foreach ($alerts as $alert) {
                $risk = ucfirst(strtolower((string) ($alert['risk'] ?? 'Informational')));
                $risk = $risk === 'Informational' ? 'Informational' : $risk;
                if (!array_key_exists($risk, $counts)) {
                    $risk = 'Informational';
                }
                $counts[$risk]++;
            }

            $status = $recordsRemaining > 0 ? 'Análisis pasivo parcialmente completado' : 'Análisis pasivo completado';

            return $this->storeZapResult([
                'status' => $status,
                'message' => $recordsRemaining > 0
                    ? 'El tiempo de espera terminó con registros todavía en la cola pasiva.'
                    : 'Spider completado y cola de análisis pasivo procesada.',
                'targetUrl' => $targetUrl,
                'zapVersion' => $zapVersion,
                'spiderPercent' => $spiderPercent,
                'recordsRemaining' => $recordsRemaining,
                'alerts' => $alerts,
                'counts' => $counts,
                'scannedAt' => now()->toDateTimeString(),
            ]);
        } catch (ConnectionException) {
            return $this->storeZapResult([
                'status' => 'ZAP no disponible',
                'message' => 'No se pudo conectar con la API local de ZAP. Inicia ZAP con la API habilitada en ' . $apiUrl . '.',
                'targetUrl' => $targetUrl,
                'alerts' => [],
                'counts' => [],
                'scannedAt' => now()->toDateTimeString(),
            ], 503);
        } catch (RequestException $exception) {
            report($exception);

            return $this->storeZapResult([
                'status' => 'Error de comunicación con ZAP',
                'message' => 'ZAP respondió con un error. Revisa que la API esté habilitada y que la clave configurada sea correcta.',
                'targetUrl' => $targetUrl,
                'alerts' => [],
                'counts' => [],
                'scannedAt' => now()->toDateTimeString(),
            ], 502);
        } catch (Throwable $exception) {
            report($exception);

            return $this->storeZapResult([
                'status' => 'Error durante el análisis',
                'message' => 'El análisis no terminó correctamente. Consulta el registro de Laravel para ver el detalle.',
                'targetUrl' => $targetUrl,
                'alerts' => [],
                'counts' => [],
                'scannedAt' => now()->toDateTimeString(),
            ], 500);
        }
    }

    private function storeZapResult(array $result, int $httpStatus = 200): JsonResponse
    {
        Cache::put('reports.zap.latest', $result, now()->addDay());

        return response()->json($result, $httpStatus);
    }
}