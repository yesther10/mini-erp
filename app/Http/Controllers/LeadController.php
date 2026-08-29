<?php

namespace App\Http\Controllers;

use App\Actions\Leads\CreateLead;
use App\Http\Requests\StoreLeadRequest;
use Illuminate\Http\RedirectResponse;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request, CreateLead $createLead): RedirectResponse
    {
        $createLead->handle($request->validated());

        return to_route('landing')->with('success', 'Your quote request was received. We will contact you soon.');
    }
}
