<?php

namespace App\Http\Requests;

use App\Enums\AssetCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
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
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'asset_category' => ['required', Rule::in(AssetCategory::values())],
            'quantity' => ['required', 'integer', 'min:1'],
            'need_summary' => ['required', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_name' => $this->trimString($this->input('company_name')),
            'contact_name' => $this->trimString($this->input('contact_name')),
            'contact_email' => Str::lower($this->trimString($this->input('contact_email'))),
            'asset_category' => $this->trimString($this->input('asset_category')),
            'quantity' => $this->input('quantity'),
            'need_summary' => $this->trimString($this->input('need_summary')),
        ]);
    }

    private function trimString(mixed $value): string
    {
        return trim((string) $value);
    }
}
