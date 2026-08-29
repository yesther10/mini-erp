<?php

namespace Tests\Feature;

use App\Enums\AssetCategory;
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
