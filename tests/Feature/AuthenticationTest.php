<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_a_hashed_password(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Portal User',
            'email' => 'portal@example.test',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'portal@example.test']);
        $this->assertTrue(Hash::check('SecurePass123', User::query()->where('email', 'portal@example.test')->value('password')));
    }

    public function test_registration_rejects_a_weak_password(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Portal User',
            'email' => 'portal@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'portal@example.test']);
    }

    public function test_registration_rejects_a_duplicate_email(): void
    {
        User::factory()->create(['email' => 'portal@example.test']);

        $this->post(route('register.store'), [
            'name' => 'Portal User',
            'email' => 'portal@example.test',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => Hash::make('SecurePass123')]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'SecurePass123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_invalid_credentials_do_not_authenticate(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'incorrect',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_cms_modules_redirect_guests_to_login(): void
    {
        foreach (['usuarios', 'contenidos', 'multimedia', 'seguridad', 'planeacion', 'evidencias'] as $module) {
            $this->get(route('cms.module', ['module' => $module]))
                ->assertRedirect(route('login'));
        }
    }

    public function test_unknown_cms_modules_return_not_found(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard/no-existe')
            ->assertNotFound();
    }

    public function test_login_and_registration_pages_are_available_to_guests(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }
}
