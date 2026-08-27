<?php

namespace App\Actions\Assets;

use App\Enums\AssetCategory;

class PresentAssetFormData
{
    /**
     * @return array<string, mixed>
     */
    public function handle(): array
    {
        return [
            'asset' => [
                'internal_code' => '',
                'serial_number' => '',
                'category' => AssetCategory::Notebook->value,
                'brand' => '',
                'model' => '',
            ],
            'categories' => $this->options(AssetCategory::cases()),
        ];
    }

    /**
     * @param  array<int, \BackedEnum>  $cases
     * @return list<array{value: string, label: string}>
     */
    private function options(array $cases): array
    {
        return array_map(
            static fn (\BackedEnum $case): array => [
                'value' => $case->value,
                'label' => str($case->value)->replace('_', ' ')->title()->toString(),
            ],
            $cases,
        );
    }
}
