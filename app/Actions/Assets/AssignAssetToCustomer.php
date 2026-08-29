<?php

namespace App\Actions\Assets;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignAssetToCustomer
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(Asset $asset, array $attributes): AssetAssignment
    {
        if (! $asset->isAssignable()) {
            throw ValidationException::withMessages([
                'asset' => 'Only available and unassigned assets can be assigned.',
            ]);
        }

        return DB::transaction(function () use ($asset, $attributes): AssetAssignment {
            $asset->update(['status' => AssetStatus::Allocated]);

            return $asset->assignment()->create($attributes);
        });
    }
}
