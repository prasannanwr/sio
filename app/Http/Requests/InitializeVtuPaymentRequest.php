<?php

namespace App\Http\Requests;

class InitializeVtuPaymentRequest extends VtuPurchaseRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'payment_methods' => ['nullable', 'array'],
            'payment_methods.*' => ['in:CARD,ACCOUNT_TRANSFER,USSD,PHONE_NUMBER'],
            // When true, skip the server-side Monnify init (the mobile SDK initializes the
            // transaction itself) — avoids "Duplicate payment reference" (code 99).
            'use_sdk' => ['nullable', 'boolean'],
        ]);
    }

    public function withValidator($validator): void
    {
        parent::withValidator($validator);

        $validator->after(function ($validator) {
            if (! $this->filled('customer_email') && ! optional($this->user())->email) {
                $validator->errors()->add('customer_email', 'A customer email is required to initialize Monnify payment.');
            }
        });
    }
}
