<?php

namespace App\Actions\Customers;

use App\Models\Customer;

class PresentCustomerFormData
{
    /**
     * @return array<string, string|null>
     */
    public function handle(?Customer $customer = null): array
    {
        return [
            'legal_name' => $customer?->legal_name ?? '',
            'cnpj' => $customer?->cnpj ?? '',
            'street' => $customer?->street ?? '',
            'number' => $customer?->number ?? '',
            'district' => $customer?->district ?? '',
            'city' => $customer?->city ?? '',
            'state' => $customer?->state ?? '',
            'zip_code' => $customer?->zip_code ?? '',
            'complement' => $customer?->complement ?? '',
            'primary_contact_name' => $customer?->primary_contact_name ?? '',
            'primary_contact_email' => $customer?->primary_contact_email ?? '',
            'primary_contact_phone' => $customer?->primary_contact_phone ?? '',
        ];
    }
}
