<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class InitializePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'amount'              => ['required', 'numeric', 'min:50'],
            // Optional: pay for a specific (cargo) order. Amount/currency are then taken from the order.
            'order_tracking_id'   => ['nullable', 'string', 'max:120'],
            // When true, skip the server-side Monnify init (the mobile SDK initializes the
            // transaction itself) — avoids "Duplicate payment reference" (code 99).
            'use_sdk'             => ['nullable', 'boolean'],
            'customer_name'       => ['required', 'string', 'max:120'],
            'customer_email'      => ['required', 'email', 'max:120'],
            'customer_phone'      => ['nullable', 'string', 'max:20'],
            'payment_description' => ['nullable', 'string', 'max:160'],
            'currency'            => ['nullable', 'string', 'size:3'],

            // Optional: lock down which methods the user sees inside the SDK.
            'payment_methods'     => ['nullable', 'array'],
            'payment_methods.*'   => ['in:CARD,ACCOUNT_TRANSFER,USSD,PHONE_NUMBER'],

            'metadata'            => ['nullable', 'array'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(new JsonResponse([
            'status'  => false,
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
