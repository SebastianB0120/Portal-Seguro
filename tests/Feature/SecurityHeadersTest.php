<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_public_pages_include_security_headers(): void
    {
        $response = $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');

        $this->assertFalse($response->headers->has('X-Powered-By'));
        $this->assertFalse($response->headers->has('Strict-Transport-Security'));
    }

    public function test_https_production_responses_include_hsts(): void
    {
        $this->app['env'] = 'production';

        $this->get('https://portal.test/')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }
}
