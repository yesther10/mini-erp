<?php

namespace Tests\Feature;

use App\Enums\AssetCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLandingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_the_public_landing_page(): void
    {
        $assetCategories = array_map(static fn (AssetCategory $category): array => [
            'value' => $category->value,
            'label' => str($category->value)->replace('_', ' ')->title()->toString(),
        ], AssetCategory::cases());

        $response = $this->get('/');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Public/Landing', false)
                ->where('heroTitle', 'Request the right equipment for your team')
                ->where('heroDescription', 'Share your equipment needs with our backoffice team and we will follow up with availability and next steps.')
                ->where('quoteSubmitUrl', '/quote-requests')
                ->where('backofficeUrl', '/login')
                ->where('assetCategories', $assetCategories)
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
                ->where('heroTitle', 'Request the right equipment for your team')
                ->where('quoteSubmitUrl', '/quote-requests')
                ->where('backofficeUrl', '/login')
                ->where('auth.user.id', $user->id)
            );
    }
}
