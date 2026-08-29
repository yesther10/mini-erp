<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLandingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_the_public_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Landing', false)
                ->where('ctaUrl', '/admin/dashboard')
                ->where('auth.user', null)
            );
    }

    public function test_authenticated_user_still_sees_the_public_landing_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Landing', false)
                ->where('ctaUrl', '/admin/dashboard')
                ->where('auth.user.id', $user->id)
            );
    }
}
