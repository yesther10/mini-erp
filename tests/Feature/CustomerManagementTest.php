<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_customer_creation_succeeds_with_valid_data(): void
    {
        $response = $this->post(route('customers.store'), $this->validPayload());

        $response->assertRedirect('/admin/customers');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'legal_name' => 'ACME LTDA',
            'cnpj' => '04252011000110',
            'primary_contact_email' => 'finance@acme.test',
            'zip_code' => '01310930',
        ]);
    }

    public function test_invalid_formats_are_rejected(): void
    {
        $payload = $this->validPayload([
            'cnpj' => '11.111.111/1111-11',
            'primary_contact_email' => 'invalid-email',
            'state' => 'XX',
            'zip_code' => '1234',
        ]);

        $response = $this->from('/admin/customers/create')->post(route('customers.store'), $payload);

        $response->assertRedirect('/admin/customers/create');
        $response->assertSessionHasErrors([
            'cnpj',
            'primary_contact_email',
            'state',
            'zip_code',
        ]);
    }

    public function test_duplicate_cnpj_is_rejected(): void
    {
        Customer::factory()->create([
            'cnpj' => '04252011000110',
        ]);

        $response = $this->from('/admin/customers/create')->post(route('customers.store'), $this->validPayload());

        $response->assertRedirect('/admin/customers/create');
        $response->assertSessionHasErrors(['cnpj']);
    }

    public function test_customer_list_search_works_by_legal_name_and_cnpj(): void
    {
        $matchingByName = Customer::factory()->create([
            'legal_name' => 'ACME INDUSTRIA LTDA',
            'cnpj' => '04252011000110',
        ]);

        $matchingByCnpj = Customer::factory()->create([
            'legal_name' => 'BETA SERVICOS LTDA',
            'cnpj' => '11222333000181',
        ]);

        Customer::factory()->create([
            'legal_name' => 'OMEGA COMERCIO LTDA',
            'cnpj' => '98765432000199',
        ]);

        $this->get('/admin/customers?search=ACME')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Customers/Index', false)
                ->where('filters.search', 'ACME')
                ->has('customers.data', 1)
                ->where('customers.data.0.id', $matchingByName->id)
            );

        $this->get('/admin/customers?search=11.222.333/0001-81')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Customers/Index', false)
                ->where('filters.search', '11.222.333/0001-81')
                ->has('customers.data', 1)
                ->where('customers.data.0.id', $matchingByCnpj->id)
            );
    }

    public function test_customer_search_treats_like_wildcards_as_literal_characters(): void
    {
        $matchingPercent = Customer::factory()->create([
            'legal_name' => '100% LOGISTICA LTDA',
            'cnpj' => '04252011000110',
        ]);

        $matchingUnderscore = Customer::factory()->create([
            'legal_name' => 'A_B TRANSPORTES LTDA',
            'cnpj' => '11222333000181',
        ]);

        Customer::factory()->create([
            'legal_name' => 'OMEGA COMERCIO LTDA',
            'cnpj' => '98765432000199',
        ]);

        $this->get('/admin/customers?search=%25')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Customers/Index', false)
                ->where('filters.search', '%')
                ->has('customers.data', 1)
                ->where('customers.data.0.id', $matchingPercent->id)
            );

        $this->get('/admin/customers?search=_')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Customers/Index', false)
                ->where('filters.search', '_')
                ->has('customers.data', 1)
                ->where('customers.data.0.id', $matchingUnderscore->id)
            );
    }

    public function test_customer_update_works(): void
    {
        $customer = Customer::factory()->create([
            'legal_name' => 'ACME LTDA',
            'cnpj' => '04252011000110',
        ]);

        $response = $this->put(route('customers.update', $customer), $this->validPayload([
            'legal_name' => 'ACME INDUSTRIA E COMERCIO LTDA',
            'street' => 'Avenida Paulista',
            'number' => '2000',
            'city' => 'Rio de Janeiro',
            'primary_contact_name' => 'Maria Silva',
            'primary_contact_email' => 'maria@acme.test',
        ]));

        $response->assertRedirect('/admin/customers');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'legal_name' => 'ACME INDUSTRIA E COMERCIO LTDA',
            'street' => 'Avenida Paulista',
            'number' => '2000',
            'city' => 'Rio de Janeiro',
            'primary_contact_name' => 'Maria Silva',
            'primary_contact_email' => 'maria@acme.test',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'legal_name' => 'ACME LTDA',
            'cnpj' => '04.252.011/0001-10',
            'street' => 'Rua das Flores',
            'number' => '123',
            'district' => 'Centro',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'zip_code' => '01310-930',
            'complement' => 'Suite 10',
            'primary_contact_name' => 'Joao Silva',
            'primary_contact_email' => 'finance@acme.test',
            'primary_contact_phone' => '1133334444',
        ], $overrides);
    }
}
