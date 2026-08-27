<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_asset_creation_succeeds_with_valid_data(): void
    {
        $response = $this->post(route('assets.store'), $this->validAssetPayload());

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('assets', [
            'internal_code' => 'AST-001',
            'serial_number' => 'SN-001',
            'category' => 'notebook',
            'brand' => 'Acme',
            'model' => 'Turbo X',
            'status' => 'available',
        ]);
    }

    public function test_asset_creation_requires_unique_internal_code_and_valid_category(): void
    {
        Asset::factory()->create([
            'internal_code' => 'AST-001',
        ]);

        $response = $this->from(route('assets.create'))->post(route('assets.store'), $this->validAssetPayload([
            'category' => 'invalid-category',
        ]));

        $response->assertRedirect(route('assets.create'));
        $response->assertSessionHasErrors(['internal_code', 'category']);
    }

    public function test_asset_creation_always_starts_as_available_even_when_a_different_status_is_submitted(): void
    {
        $response = $this->post(route('assets.store'), $this->validAssetPayload([
            'status' => 'retired',
        ]));

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('assets', [
            'internal_code' => 'AST-001',
            'status' => 'available',
        ]);
    }

    public function test_asset_list_can_be_filtered_by_internal_code_and_category(): void
    {
        $matchingByCode = Asset::factory()->create([
            'internal_code' => 'AST-ACME-001',
            'category' => 'notebook',
        ]);

        $matchingByCategory = Asset::factory()->create([
            'internal_code' => 'AST-BETA-002',
            'category' => 'desktop',
        ]);

        Asset::factory()->create([
            'internal_code' => 'AST-GAMMA-003',
            'category' => 'printer',
        ]);

        $this->get(route('assets.index', ['search' => 'ACME']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Assets/Index', false)
                ->where('filters.search', 'ACME')
                ->has('assets', 1)
                ->where('assets.0.id', $matchingByCode->id)
            );

        $this->get(route('assets.index', ['category' => 'desktop']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Assets/Index', false)
                ->where('filters.category', 'desktop')
                ->has('assets', 1)
                ->where('assets.0.id', $matchingByCategory->id)
            );
    }

    public function test_asset_search_treats_percent_and_underscore_as_literal_characters(): void
    {
        $percentMatch = Asset::factory()->create(['internal_code' => 'AST%001']);
        $underscoreMatch = Asset::factory()->create(['internal_code' => 'AST_002']);

        Asset::factory()->create(['internal_code' => 'AST-003']);
        Asset::factory()->create(['internal_code' => 'ASTX004']);

        $this->get(route('assets.index', ['search' => '%']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Assets/Index', false)
                ->where('filters.search', '%')
                ->has('assets', 1)
                ->where('assets.0.id', $percentMatch->id)
            );

        $this->get(route('assets.index', ['search' => '_']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Assets/Index', false)
                ->where('filters.search', '_')
                ->has('assets', 1)
                ->where('assets.0.id', $underscoreMatch->id)
            );
    }

    public function test_available_asset_can_be_assigned_to_a_customer(): void
    {
        $asset = Asset::factory()->create([
            'status' => 'available',
        ]);
        $customer = Customer::factory()->create();

        $response = $this->post(route('assets.assignments.store', $asset), [
            'customer_id' => $customer->id,
            'allocated_at' => '2026-08-26',
            'note' => 'Installed on-site',
        ]);

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('asset_assignments', [
            'asset_id' => $asset->id,
            'customer_id' => $customer->id,
            'note' => 'Installed on-site',
        ]);

        $this->assertSame('2026-08-26', $asset->fresh()->assignment->allocated_at->toDateString());

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'status' => 'allocated',
        ]);
    }

    public function test_maintenance_or_retired_assets_cannot_be_assigned(): void
    {
        $customer = Customer::factory()->create();

        foreach (['maintenance', 'retired'] as $status) {
            $asset = Asset::factory()->create(['status' => $status]);

            $response = $this->from(route('assets.assignments.create', $asset))->post(route('assets.assignments.store', $asset), [
                'customer_id' => $customer->id,
                'allocated_at' => '2026-08-26',
                'note' => null,
            ]);

            $response->assertRedirect(route('assets.assignments.create', $asset));
            $response->assertSessionHasErrors(['asset']);

            $this->assertDatabaseMissing('asset_assignments', [
                'asset_id' => $asset->id,
            ]);
        }
    }

    public function test_non_assignable_assets_cannot_access_the_assignment_form(): void
    {
        $availableAssignedAsset = Asset::factory()->create([
            'status' => 'available',
        ]);
        $availableAssignedAsset->assignment()->create([
            'customer_id' => Customer::factory()->create()->id,
            'allocated_at' => '2026-08-20',
            'note' => 'Already linked',
        ]);

        $assets = [
            Asset::factory()->create(['status' => 'maintenance']),
            Asset::factory()->create(['status' => 'retired']),
            Asset::factory()->create(['status' => 'allocated']),
            $availableAssignedAsset,
        ];

        foreach ($assets as $asset) {
            $this->get(route('assets.assignments.create', $asset))
                ->assertRedirect(route('assets.index'))
                ->assertSessionHas('error', 'Only available and unassigned assets can be assigned.');
        }
    }

    public function test_allocated_asset_cannot_be_assigned_twice(): void
    {
        $asset = Asset::factory()->create([
            'status' => 'allocated',
        ]);
        $firstCustomer = Customer::factory()->create();
        $secondCustomer = Customer::factory()->create();

        $asset->assignment()->create([
            'customer_id' => $firstCustomer->id,
            'allocated_at' => '2026-08-20',
            'note' => 'Already allocated',
        ]);

        $response = $this->from(route('assets.assignments.create', $asset))->post(route('assets.assignments.store', $asset), [
            'customer_id' => $secondCustomer->id,
            'allocated_at' => '2026-08-26',
            'note' => 'Second attempt',
        ]);

        $response->assertRedirect(route('assets.assignments.create', $asset));
        $response->assertSessionHasErrors(['asset']);

        $this->assertDatabaseCount('asset_assignments', 1);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validAssetPayload(array $overrides = []): array
    {
        return array_merge([
            'internal_code' => 'AST-001',
            'serial_number' => 'SN-001',
            'category' => 'notebook',
            'brand' => 'Acme',
            'model' => 'Turbo X',
        ], $overrides);
    }
}
