<?php

namespace App\Http\Requests;

use App\Models\Customer;
use App\Rules\ValidCnpj;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class CustomerRequest extends FormRequest
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
            'legal_name' => ['required', 'string', 'max:255'],
            'cnpj' => [
                'required',
                'string',
                'size:14',
                new ValidCnpj,
                Rule::unique('customers', 'cnpj')->ignore($this->customer?->id),
            ],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:50'],
            'district' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2', Rule::in(Customer::BRAZILIAN_UF_CODES)],
            'zip_code' => ['required', 'string', 'regex:/^\d{8}$/'],
            'complement' => ['nullable', 'string', 'max:255'],
            'primary_contact_name' => ['required', 'string', 'max:255'],
            'primary_contact_email' => ['required', 'email', 'max:255'],
            'primary_contact_phone' => ['required', 'string', 'regex:/^\d{10,11}$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'legal_name' => $this->trimString($this->input('legal_name')),
            'cnpj' => $this->digitsOnly($this->input('cnpj')),
            'street' => $this->trimString($this->input('street')),
            'number' => $this->trimString($this->input('number')),
            'district' => $this->trimString($this->input('district')),
            'city' => $this->trimString($this->input('city')),
            'state' => strtoupper($this->trimString($this->input('state'))),
            'zip_code' => $this->digitsOnly($this->input('zip_code')),
            'complement' => $this->nullableTrimmedString($this->input('complement')),
            'primary_contact_name' => $this->trimString($this->input('primary_contact_name')),
            'primary_contact_email' => $this->trimString($this->input('primary_contact_email')),
            'primary_contact_phone' => $this->digitsOnly($this->input('primary_contact_phone')),
        ]);
    }

    private function digitsOnly(mixed $value): string
    {
        return preg_replace('/\D/', '', (string) $value) ?? '';
    }

    private function trimString(mixed $value): string
    {
        return trim((string) $value);
    }

    private function nullableTrimmedString(mixed $value): ?string
    {
        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
