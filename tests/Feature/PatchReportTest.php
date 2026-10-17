<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PatchReportTest extends TestCase
{
    public function test_patch_report_contains_project_code_zap_status_and_capture_tools(): void
    {
        $response = $this->get(route('reports.patches'));

        $response->assertOk();
        $response->assertViewIs('reports.patch-management');
        $response->assertSee('Gestión de parches y OWASP ZAP');
        $response->assertSee('resources/views/home.blade.php');
        $response->assertSee('config/zap.php');
        $response->assertSee('patch-manual-heading');
        $response->assertSee('Integración automática con la API de OWASP ZAP');
        $response->assertSee('spider/action/scan');
        $response->assertSee('composer audit');
        $response->assertSee('Pendiente de análisis automático');
        $response->assertSee('Ejecutar análisis pasivo');
        $response->assertSee('/informes/parches/zap-scan');
        $response->assertSee('capture-gallery');
        $response->assertSee('Preparando capturas del informe...');
        $response->assertSee('js/patch-report.js');
    }

    public function test_passive_zap_scan_reports_actual_alerts_for_local_target(): void
    {
        config([
            'zap.api_url' => 'http://127.0.0.1:8080',
            'zap.api_key' => '',
            'zap.target_url' => 'http://127.0.0.1:8000',
            'zap.spider_timeout_seconds' => 2,
            'zap.passive_scan_timeout_seconds' => 2,
        ]);

        Http::fake(function ($request) {
            $url = $request->url();

            return match (true) {
                str_contains($url, '/JSON/core/view/version/') => Http::response(['version' => '2.16.1']),
                str_contains($url, '/JSON/spider/action/scan/') => Http::response(['scan' => '1']),
                str_contains($url, '/JSON/spider/view/status/') => Http::response(['status' => '100']),
                str_contains($url, '/JSON/pscan/view/recordsToScan/') => Http::response(['recordsToScan' => '0']),
                str_contains($url, '/JSON/alert/view/alerts/') => Http::response(['alerts' => [[
                    'risk' => 'Low',
                    'alert' => 'X-Content-Type-Options Header Missing',
                    'url' => 'http://127.0.0.1:8000/',
                ]]]),
                default => Http::response([], 404),
            };
        });

        $response = $this->postJson(route('reports.patches.zap-scan'));

        $response->assertOk()
            ->assertJsonPath('status', 'Análisis pasivo completado')
            ->assertJsonPath('zapVersion', '2.16.1')
            ->assertJsonPath('counts.Low', 1)
            ->assertJsonPath('alerts.0.alert', 'X-Content-Type-Options Header Missing');

        Http::assertSent(fn ($request) => str_contains($request->url(), '/JSON/spider/action/scan/'));
        Http::assertSent(fn ($request) => str_contains($request->url(), '/JSON/alert/view/alerts/'));
    }

    public function test_zap_scan_rejects_non_local_targets(): void
    {
        config(['zap.target_url' => 'https://example.com']);

        $response = $this->postJson(route('reports.patches.zap-scan'));

        $response->assertUnprocessable()
            ->assertJsonPath('status', 'Escaneo detenido');
        Http::assertNothingSent();
    }

    public function test_zap_scan_reports_when_the_local_service_is_unavailable(): void
    {
        Http::fake(['*' => Http::failedConnection()]);

        $response = $this->postJson(route('reports.patches.zap-scan'));

        $response->assertServiceUnavailable()
            ->assertJsonPath('status', 'ZAP no disponible')
            ->assertJsonPath('targetUrl', 'http://127.0.0.1:8000');
    }
}
