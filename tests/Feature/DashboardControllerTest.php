<?php

namespace Tests\Feature;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_dashboard_returns_stats_and_customers(): void
    {
        $this->assertSame('/admin/dashboard', parse_url(route('dashboard'), PHP_URL_PATH));

        $customer1 = Customer::factory()->create(['legal_name' => 'ACME LTDA']);
        $customer2 = Customer::factory()->create(['legal_name' => 'BETA LTDA']);

        $asset1 = Asset::factory()->create(['status' => AssetStatus::Available]);
        $asset2 = Asset::factory()->create(['status' => AssetStatus::Available]);
        $asset3 = Asset::factory()->create(['status' => AssetStatus::Available]);

        AssetAssignment::factory()->create([
            'asset_id' => $asset1->id,
            'customer_id' => $customer1->id,
        ]);
        AssetAssignment::factory()->create([
            'asset_id' => $asset2->id,
            'customer_id' => $customer1->id,
        ]);

        $response = $this->get('/admin/dashboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->where('stats.totalCustomers', 2)
            ->where('stats.totalAssets', 3)
            ->where('stats.totalAssigned', 2)
            ->has('customers', 2)
            ->where('customers.0.legal_name', 'ACME LTDA')
            ->has('customers.0.assets', 2)
        );
    }

    public function test_dashboard_shows_customer_with_no_assets(): void
    {
        Customer::factory()->create(['legal_name' => 'EMPTY CORP']);

        $response = $this->get('/admin/dashboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->where('stats.totalCustomers', 1)
            ->where('stats.totalAssets', 0)
            ->where('stats.totalAssigned', 0)
            ->has('customers', 1)
            ->where('customers.0.legal_name', 'EMPTY CORP')
            ->where('customers.0.assets', [])
        );
    }

    public function test_dashboard_with_no_data(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->where('stats.totalCustomers', 0)
            ->where('stats.totalAssets', 0)
            ->where('stats.totalAssigned', 0)
            ->has('customers', 0)
        );
    }

    public function test_dashboard_customers_are_ordered_by_legal_name(): void
    {
        Customer::factory()->create(['legal_name' => 'ZETA LTDA']);
        Customer::factory()->create(['legal_name' => 'ALPHA LTDA']);
        Customer::factory()->create(['legal_name' => 'MU LTDA']);

        $response = $this->get('/admin/dashboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard', false)
            ->where('customers.0.legal_name', 'ALPHA LTDA')
            ->where('customers.1.legal_name', 'MU LTDA')
            ->where('customers.2.legal_name', 'ZETA LTDA')
        );
    }
}
