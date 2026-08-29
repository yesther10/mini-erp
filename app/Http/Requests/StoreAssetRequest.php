<?php

namespace App\Http\Requests;

use App\Enums\AssetCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'internal_code' => ['required', 'string', 'max:255', Rule::unique('assets', 'internal_code')],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'category' => ['required', Rule::in(AssetCategory::values())],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'internal_code' => trim((string) $this->input('internal_code')),
            'serial_number' => $this->nullableTrimmedString($this->input('serial_number')),
            'category' => trim((string) $this->input('category')),
            'brand' => trim((string) $this->input('brand')),
            'model' => trim((string) $this->input('model')),
        ]);
    }

    private function nullableTrimmedString(mixed $value): ?string
    {
        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
