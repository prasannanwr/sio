<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class VtuCustomerValidationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_type' => ['required', 'string', Rule::in(array_keys(config('vtu.services', [])))],
            'provider_code' => ['required', 'string', 'max:80'],
            'product_code' => ['nullable', 'string', 'max:120'],
            'customer_reference' => ['required', 'string', 'max:120'],
            'amount' => ['nullable', 'numeric', 'min:1'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $serviceType = (string) $this->input('service_type');
            $service = config("vtu.services.{$serviceType}");

            if (! is_array($service)) {
                return;
            }

            if (($service['requires_product'] ?? false) && ! $this->filled('product_code')) {
                $validator->errors()->add('product_code', 'A product code is required for this service.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('service_type')) {
            $this->merge(['service_type' => strtolower((string) $this->input('service_type'))]);
        }
    }

    protected function failedValidation(ValidatorContract $validator)
    {
        throw new HttpResponseException(new JsonResponse([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
