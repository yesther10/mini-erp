<?php

namespace App\Actions\Customers;

use App\Models\Customer;

class CreateCustomer
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes): Customer
    {
        return Customer::create($attributes);
    }
}
