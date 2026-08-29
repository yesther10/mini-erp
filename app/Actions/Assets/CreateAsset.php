<?php

namespace App\Actions\Assets;

use App\Enums\AssetStatus;
use App\Models\Asset;

class CreateAsset
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(array $attributes): Asset
    {
        return Asset::create([
            ...$attributes,
            'status' => AssetStatus::Available->value,
        ]);
    }
}
