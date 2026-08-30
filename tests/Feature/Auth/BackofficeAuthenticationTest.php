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

    public function test_authenticated_user_is_redirected_from_login_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_guest_is_redirected_from_admin_dashboard_to_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirectToRoute('login');
    }

    public function test_guest_is_redirected_from_admin_customers_to_login(): void
    {
        $response = $this->get('/admin/customers');

        $response->assertRedirectToRoute('login');
    }

    public function test_guest_is_redirected_from_admin_assets_to_login(): void
    {
        $response = $this->get('/admin/assets');

        $response->assertRedirectToRoute('login');
    }

    public function test_guest_is_redirected_from_admin_asset_assignment_to_login(): void
    {
        $asset = Asset::factory()->create();

        $response = $this->get("/admin/assets/{$asset->id}/assign");

        $response->assertRedirectToRoute('login');
    }

    public function test_guest_is_redirected_from_admin_leads_to_login(): void
    {
        $response = $this->get('/admin/leads');

        $response->assertRedirectToRoute('login');
    }

    public function test_authenticated_user_can_access_admin_leads_without_role_checks(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/leads');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page->component('Leads/Index', false));
    }

    public function test_valid_credentials_redirect_to_the_intended_admin_page(): void
    {
        $user = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => 'password',
        ]);

        $this->get('/admin/customers');

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/customers');

        $this->assertAuthenticatedAs($user);
    }

    public function test_valid_credentials_fall_back_to_dashboard_when_no_intended_route_exists(): void
    {
        $user = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => 'password',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');

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

        $response = $this->get('/admin/customers');

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
}
