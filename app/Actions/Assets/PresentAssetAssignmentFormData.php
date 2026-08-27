<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use App\Models\Customer;

class PresentAssetAssignmentFormData
{
    /**
     * @return array<string, mixed>
     */
    public function handle(Asset $asset): array
    {
        return [
            'asset' => [
                'id' => $asset->id,
                'internal_code' => $asset->internal_code,
                'category' => $asset->category->value,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'status' => $asset->status->value,
            ],
            'customers' => Customer::query()
                ->orderBy('legal_name')
                ->get(['id', 'legal_name'])
                ->map(fn (Customer $customer): array => [
                    'id' => $customer->id,
                    'legal_name' => $customer->legal_name,
                ])
                ->all(),
            'assignment' => [
                'customer_id' => '',
                'allocated_at' => now()->toDateString(),
                'note' => '',
            ],
        ];
    }
}
