<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Plan de gestión de parches');
        $response->assertSee('Inventario');
        $response->assertSee('Vulnerabilidades');
        $response->assertSee('Aplicar parche');
        $response->assertSee('Pruebas');
        $response->assertSee('Monitoreo');
        $response->assertSee('Manual de operación / Seguridad de aplicaciones');
        $response->assertSee('Gestión de parches y análisis con OWASP ZAP');
        $response->assertSee('OWASP ZAP', false);
        $response->assertSee('Analiza solo sistemas autorizados');
        $response->assertSee('composer audit');
        $response->assertSee('composer update proveedor/paquete --with-dependencies');
        $response->assertSee('php artisan test');
    }
}
