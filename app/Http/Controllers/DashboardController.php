<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Customer;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $totalCustomers = Customer::count();
        $totalAssets = Asset::count();
        $totalAssigned = Asset::whereHas('assignment')->count();

        $customers = Customer::query()
            ->with(['assetAssignments.asset'])
            ->orderBy('legal_name')
            ->get()
            ->map(fn (Customer $customer): array => [
                'id' => $customer->id,
                'legal_name' => $customer->legal_name,
                'city' => $customer->city,
                'state' => $customer->state,
                'assets' => $customer->assetAssignments
                    ->map(fn ($assignment): array => [
                        'internal_code' => $assignment->asset->internal_code,
                        'brand' => $assignment->asset->brand,
                        'model' => $assignment->asset->model,
                        'status' => $assignment->asset->status->value,
                    ])
                    ->all(),
            ]);

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalCustomers' => $totalCustomers,
                'totalAssets' => $totalAssets,
                'totalAssigned' => $totalAssigned,
            ],
            'customers' => $customers,
        ]);
    }
}
