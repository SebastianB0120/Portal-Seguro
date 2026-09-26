<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsMenuPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_cms_menu_pages_load_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $modules = ['usuarios', 'contenidos', 'multimedia', 'seguridad', 'planeacion', 'evidencias'];

        foreach ($modules as $module) {
            $response = $this->actingAs($user)->get('/dashboard/' . $module);
            $response->assertStatus(200);
            $response->assertViewIs('cms.module');
        }
    }
}
