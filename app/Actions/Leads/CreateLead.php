<?php

namespace App\Actions\Leads;

use App\Models\Lead;

class CreateLead
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes): Lead
    {
        return Lead::create($attributes);
    }
}
