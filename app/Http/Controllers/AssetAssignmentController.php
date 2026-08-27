<?php

namespace App\Http\Controllers;

use App\Actions\Assets\AssignAssetToCustomer;
use App\Actions\Assets\PresentAssetAssignmentFormData;
use App\Http\Requests\StoreAssetAssignmentRequest;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AssetAssignmentController extends Controller
{
    public function create(Asset $asset, PresentAssetAssignmentFormData $presentAssetAssignmentFormData): Response|RedirectResponse
    {
        if (! $asset->isAssignable()) {
            return to_route('assets.index')->with('error', 'Only available and unassigned assets can be assigned.');
        }

        return Inertia::render('Assets/Assign', $presentAssetAssignmentFormData->handle($asset));
    }

    public function store(StoreAssetAssignmentRequest $request, Asset $asset, AssignAssetToCustomer $assignAssetToCustomer): RedirectResponse
    {
        $assignAssetToCustomer->handle($asset, $request->validated());

        return to_route('assets.index')->with('success', 'Asset assigned successfully.');
    }
}
