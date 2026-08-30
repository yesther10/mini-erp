<?php

namespace App\Http\Controllers;

use App\Actions\Leads\CreateLead;
use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class LeadController extends Controller
{
    public function index(): InertiaResponse
    {
        $leads = Lead::latest()->paginate(15);

        return Inertia::render('Leads/Index', [
            'leads' => $leads,
        ]);
    }

    public function store(StoreLeadRequest $request, CreateLead $createLead): RedirectResponse
    {
        $createLead->handle($request->validated());

        return to_route('landing')->with('success', 'Your quote request was received. We will contact you soon.');
    }
}
