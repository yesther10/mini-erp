<?php

namespace App\Http\Controllers;

use App\Actions\Customers\CreateCustomer;
use App\Actions\Customers\PresentCustomerFormData;
use App\Actions\Customers\UpdateCustomer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));

        $customers = Customer::query()
            ->search($search)
            ->orderBy('legal_name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Customer $customer): array => [
                'id' => $customer->id,
                'legal_name' => $customer->legal_name,
                'cnpj' => $customer->cnpj,
                'city' => $customer->city,
                'state' => $customer->state,
                'primary_contact_name' => $customer->primary_contact_name,
                'primary_contact_email' => $customer->primary_contact_email,
            ]);

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(PresentCustomerFormData $presentCustomerFormData): Response
    {
        return Inertia::render('Customers/Form', [
            'pageTitle' => 'Create customer',
            'submitUrl' => route('customers.store'),
            'method' => 'post',
            'customer' => $presentCustomerFormData->handle(),
        ]);
    }

    public function store(StoreCustomerRequest $request, CreateCustomer $createCustomer): RedirectResponse
    {
        $createCustomer->handle($request->validated());

        return to_route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer, PresentCustomerFormData $presentCustomerFormData): Response
    {
        return Inertia::render('Customers/Form', [
            'pageTitle' => 'Edit customer',
            'submitUrl' => route('customers.update', $customer),
            'method' => 'put',
            'customer' => $presentCustomerFormData->handle($customer),
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer, UpdateCustomer $updateCustomer): RedirectResponse
    {
        $updateCustomer->handle($customer, $request->validated());

        return to_route('customers.index')->with('success', 'Customer updated successfully.');
    }
}
