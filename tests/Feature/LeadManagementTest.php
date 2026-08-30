<?php

namespace Tests\Feature;

use App\Enums\AssetCategory;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_quote_request_creates_a_lead_and_redirects_back_with_a_success_message(): void
    {
        $response = $this->from('/')->post('/quote-requests', $this->validPayload());

        $response->assertRedirect('/');
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success', 'Your quote request was received. We will contact you soon.');

        $this->assertDatabaseHas('leads', [
            'company_name' => 'ACME LTDA',
            'contact_name' => 'Ana Silva',
            'contact_email' => 'ana.silva@example.com',
            'asset_category' => AssetCategory::Notebook->value,
            'quantity' => 12,
            'need_summary' => 'Need notebooks for a new onboarding batch.',
        ]);
    }

    public function test_invalid_quote_request_is_rejected_with_field_errors(): void
    {
        $response = $this->from('/')->post('/quote-requests', [
            'company_name' => '',
            'contact_name' => '',
            'contact_email' => 'invalid-email',
            'asset_category' => 'server',
            'quantity' => 0,
            'need_summary' => '',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors([
            'company_name',
            'contact_name',
            'contact_email',
            'asset_category',
            'quantity',
            'need_summary',
        ]);

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_authenticated_user_can_view_admin_leads_list_in_newest_first_order(): void
    {
        $user = User::factory()->create();

        $older = Lead::factory()->create(['company_name' => 'Older Corp', 'created_at' => now()->subDays(2)]);
        $newer = Lead::factory()->create(['company_name' => 'Newer Corp', 'created_at' => now()]);

        $response = $this->actingAs($user)->get('/admin/leads');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Leads/Index', false)
                ->has('leads.data', 2)
                ->where('leads.data.0.company_name', 'Newer Corp')
                ->where('leads.data.1.company_name', 'Older Corp')
            );
    }

    public function test_admin_leads_list_shows_submitted_request_details(): void
    {
        $user = User::factory()->create();

        Lead::factory()->create([
            'company_name' => 'ACME LTDA',
            'contact_name' => 'Ana Silva',
            'contact_email' => 'ana.silva@example.com',
            'asset_category' => AssetCategory::Notebook->value,
            'quantity' => 12,
            'need_summary' => 'Need notebooks for onboarding.',
        ]);

        $response = $this->actingAs($user)->get('/admin/leads');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Leads/Index', false)
                ->where('leads.data.0.company_name', 'ACME LTDA')
                ->where('leads.data.0.contact_name', 'Ana Silva')
                ->where('leads.data.0.contact_email', 'ana.silva@example.com')
                ->where('leads.data.0.asset_category', AssetCategory::Notebook->value)
                ->where('leads.data.0.quantity', 12)
                ->where('leads.data.0.need_summary', 'Need notebooks for onboarding.')
            );
    }

    public function test_admin_leads_list_shows_empty_state_when_no_leads_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/leads');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Leads/Index', false)
                ->where('leads.data', [])
            );
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'company_name' => '  ACME LTDA  ',
            'contact_name' => '  Ana Silva  ',
            'contact_email' => '  Ana.Silva@Example.com  ',
            'asset_category' => AssetCategory::Notebook->value,
            'quantity' => 12,
            'need_summary' => '  Need notebooks for a new onboarding batch.  ',
        ], $overrides);
    }
}
