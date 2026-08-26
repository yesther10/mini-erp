<?php

namespace App\Actions\Customers;

use App\Models\Customer;

class UpdateCustomer
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Customer $customer, array $attributes): Customer
    {
        $customer->update($attributes);

        return $customer;
    }
}
