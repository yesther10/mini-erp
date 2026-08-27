<?php

namespace App\Http\Controllers;

use App\Actions\Assets\CreateAsset;
use App\Actions\Assets\PresentAssetFormData;
use App\Enums\AssetCategory;
use App\Http\Requests\StoreAssetRequest;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssetController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));
        $category = $request->string('category')->trim()->toString();
        $category = $category === '' ? null : $category;

        $assets = Asset::query()
            ->with(['assignment.customer'])
            ->filter($search, $category)
            ->orderBy('internal_code')
            ->get()
            ->map(fn (Asset $asset): array => [
                'id' => $asset->id,
                'internal_code' => $asset->internal_code,
                'serial_number' => $asset->serial_number,
                'category' => $asset->category->value,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'status' => $asset->status->value,
                'customer' => $asset->assignment?->customer?->legal_name,
                'can_assign' => $asset->isAssignable(),
            ])
            ->all();

        return Inertia::render('Assets/Index', [
            'assets' => $assets,
            'filters' => [
                'search' => $search,
                'category' => $category,
            ],
            'categories' => array_map(static fn (AssetCategory $category): array => [
                'value' => $category->value,
                'label' => str($category->value)->replace('_', ' ')->title()->toString(),
            ], AssetCategory::cases()),
        ]);
    }

    public function create(PresentAssetFormData $presentAssetFormData): Response
    {
        return Inertia::render('Assets/Form', $presentAssetFormData->handle());
    }

    public function store(StoreAssetRequest $request, CreateAsset $createAsset): RedirectResponse
    {
        $createAsset->handle($request->validated());

        return to_route('assets.index')->with('success', 'Asset created successfully.');
    }
}
