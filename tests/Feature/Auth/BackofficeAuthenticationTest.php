<?php

namespace Tests\Feature\Auth;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackofficeAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_the_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Auth/Login', false)
                ->where('auth.user', null)
            );
    }

    public function test_authenticated_user_is_redirected_from_login_to_customers_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirectToRoute('customers.index');
    }

    public function test_guest_is_redirected_from_customers_index_to_login(): void
    {
        $this->assertGuestRedirectsToLogin('customers.index');
    }

    public function test_guest_is_redirected_from_assets_index_to_login(): void
    {
        $asset = Asset::factory()->create();

        $this->assertGuestRedirectsToLogin('assets.index');
        $this->assertGuestRedirectsToLogin('assets.assignments.create', [$asset]);
    }

    public function test_valid_credentials_redirect_to_the_intended_backoffice_page(): void
    {
        $user = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => 'password',
        ]);

        $this->get(route('customers.index'));

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirectToRoute('customers.index');

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_redirect_back_with_an_authentication_error(): void
    {
        $user = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => 'password',
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);

        $this->assertGuest();
    }

    public function test_logout_ends_the_session_and_redirects_to_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirectToRoute('login');

        $this->assertGuest();
    }

    public function test_logged_out_user_cannot_access_protected_routes_anymore(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('logout'));

        $response = $this->get(route('customers.index'));

        $response->assertRedirectToRoute('login');
        $this->assertGuest();
    }

    public function test_authenticated_backoffice_pages_include_shared_auth_user_props(): void
    {
        $user = User::factory()->create([
            'name' => 'Backoffice Manager',
            'email' => 'manager@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('customers.index'));

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Customers/Index', false)
                ->where('auth.user.id', $user->id)
                ->where('auth.user.name', $user->name)
                ->where('auth.user.email', $user->email)
            );
    }

    /**
     * @param  array<int, mixed>|array<string, mixed>  $parameters
     */
    private function assertGuestRedirectsToLogin(string $route, array $parameters = []): void
    {
        $response = $this->get(route($route, $parameters));

        $response->assertRedirectToRoute('login');
    }
}
