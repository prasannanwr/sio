<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\VtuTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class VtuPaymentService
{
    public function __construct(
        protected MonnifyService $monnify,
        protected VtuProviderService $provider
    ) {
    }

    /**
     * Create a pending VTU transaction, initialize Monnify, and return the
     * checkout details the mobile app needs to collect payment.
     *
     * @param  array<string,mixed>  $data
     * @return array{vtu_transaction: VtuTransaction, payment: Transaction, monnify: array<string,mixed>}
     */
    public function initializePayment(array $data, User $user): array
    {
        $vtuReference = $this->generateVtuReference();
        $paymentReference = $this->generatePaymentReference();
        $currency = $data['currency'] ?? config('vtu.currency', 'NGN');
        $customerName = $this->customerName($data, $user);
        $customerEmail = $this->customerEmail($data, $user);
        $description = $this->descriptionFor($data['service_type'], $data['customer_reference']);
        $metadata = $data['metadata'] ?? [];

        if ($customerEmail === '') {
            throw new RuntimeException('A customer email is required to initialize Monnify payment.');
        }

        // Refuse purchases the provider would reject before the customer is charged.
        $this->provider->preflight($data);

        $providerPayload = $this->providerPayload($data, $user, $vtuReference, $currency);

        [$vtuTransaction, $payment] = DB::transaction(function () use (
            $user,
            $vtuReference,
            $paymentReference,
            $data,
            $customerName,
            $customerEmail,
            $currency,
            $description,
            $metadata,
            $providerPayload
        ) {
            $vtuTransaction = VtuTransaction::create([
                'user_id' => $user->id,
                'reference' => $vtuReference,
                'payment_reference' => $paymentReference,
                'service_type' => $data['service_type'],
                'provider_code' => $data['provider_code'],
                'product_code' => $data['product_code'] ?? null,
                'customer_reference' => $data['customer_reference'],
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $data['customer_phone'] ?? $user->phone,
                'amount' => $data['amount'],
                'currency' => $currency,
                'status' => VtuTransaction::STATUS_PENDING,
                'description' => $description,
                'metadata' => $metadata ?: null,
                'request_payload' => $providerPayload,
            ]);

            $payment = Transaction::create([
                'payment_reference' => $paymentReference,
                'user_id' => $user->id,
                'transaction_type' => 'vtu',
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $data['customer_phone'] ?? $user->phone,
                'amount' => $data['amount'],
                'currency' => $currency,
                'payment_description' => $description,
                'metadata' => $this->paymentMetadata($metadata, $vtuTransaction),
                'payment_provider' => 'monnify',
                'status' => Transaction::STATUS_PENDING,
            ]);

            return [$vtuTransaction, $payment];
        });

        // SDK flow: the Monnify mobile SDK initializes the transaction itself, so we must NOT
        // pre-initialize it here — doing so causes "Duplicate payment reference" (code 99).
        if (! empty($data['use_sdk'])) {
            return [
                'vtu_transaction' => $vtuTransaction->fresh(),
                'payment' => $payment->fresh(),
                'monnify' => null,
            ];
        }

        try {
            $monnifyResponse = $this->monnify->initializeTransaction([
                'amount' => (float) $payment->amount,
                'customerName' => $payment->customer_name,
                'customerEmail' => $payment->customer_email,
                'paymentReference' => $payment->payment_reference,
                'paymentDescription' => $payment->payment_description,
                'currencyCode' => $payment->currency,
                'paymentMethods' => $data['payment_methods'] ?? null,
                'metadata' => $this->monnifyMetadata($metadata, $vtuTransaction),
            ]);
        } catch (Throwable $e) {
            $payment->update([
                'status' => Transaction::STATUS_FAILED,
                'gateway_response' => ['error' => $e->getMessage()],
            ]);

            $vtuTransaction->update([
                'status' => VtuTransaction::STATUS_FAILED,
                'provider_response' => ['payment_error' => $e->getMessage()],
            ]);

            throw $e;
        }

        $payment->update([
            'transaction_reference' => $monnifyResponse['transactionReference'] ?? null,
            'checkout_url' => $monnifyResponse['checkoutUrl'] ?? null,
            'gateway_response' => $monnifyResponse,
        ]);

        return [
            'vtu_transaction' => $vtuTransaction->fresh(),
            'payment' => $payment->fresh(),
            'monnify' => $monnifyResponse,
        ];
    }

    public function fulfillPaidPayment(Transaction $payment): ?VtuTransaction
    {
        if (! $payment->isSuccessful()) {
            return null;
        }

        $transaction = VtuTransaction::where('payment_reference', $payment->payment_reference)->first();

        if (! $transaction) {
            return null;
        }

        return $this->fulfillIfPaid($transaction, $payment);
    }

    public function fulfillIfPaid(VtuTransaction $transaction, ?Transaction $payment = null): VtuTransaction
    {
        $payment = $payment ?: $transaction->payment;

        if (! $payment || ! $payment->isSuccessful()) {
            return $transaction->fresh();
        }

        $paidAmount = (float) ($payment->amount_paid ?: $payment->amount);

        if ($paidAmount < (float) $transaction->amount) {
            if ($transaction->isPending()) {
                $transaction->update([
                    'status' => VtuTransaction::STATUS_FAILED,
                    'provider_response' => [
                        'error' => 'Payment amount is lower than the VTU transaction amount.',
                        'payment_reference' => $payment->payment_reference,
                        'amount_paid' => $paidAmount,
                    ],
                ]);
            }

            return $transaction->fresh();
        }

        $started = VtuTransaction::whereKey($transaction->id)
            ->where('status', VtuTransaction::STATUS_PENDING)
            ->update(['status' => VtuTransaction::STATUS_PROCESSING]);

        if (! $started) {
            return $transaction->fresh();
        }

        $transaction = $transaction->fresh();

        try {
            $providerResponse = $this->provider->purchase($this->providerPayloadFromTransaction($transaction));
        } catch (Throwable $e) {
            Log::warning('Paid VTU fulfillment failed', [
                'vtu_reference' => $transaction->reference,
                'payment_reference' => $payment->payment_reference,
                'error' => $e->getMessage(),
            ]);

            $transaction->update([
                'status' => VtuTransaction::STATUS_FAILED,
                'provider_response' => ['error' => $e->getMessage()],
            ]);

            return $transaction->fresh();
        }

        $this->syncTransactionFromProvider($transaction, $providerResponse);

        return $transaction->fresh();
    }

    /**
     * @param  array<string,mixed>  $providerResponse
     */
    public function syncTransactionFromProvider(VtuTransaction $transaction, array $providerResponse): void
    {
        $status = $this->provider->normalizeStatus($providerResponse);

        if ($transaction->status === VtuTransaction::STATUS_SUCCESSFUL
            && in_array($status, [VtuTransaction::STATUS_PENDING, VtuTransaction::STATUS_PROCESSING], true)) {
            $status = VtuTransaction::STATUS_SUCCESSFUL;
        }

        $update = [
            'status' => $status,
            'provider_reference' => $this->provider->providerReference($providerResponse) ?: $transaction->provider_reference,
            'provider_response' => $providerResponse,
        ];

        if ($status === VtuTransaction::STATUS_SUCCESSFUL && ! $transaction->fulfilled_at) {
            $update['fulfilled_at'] = now();
        }

        $transaction->update($update);
    }

    /**
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    protected function providerPayload(array $data, User $user, string $reference, string $currency): array
    {
        return [
            'reference' => $reference,
            'service_type' => $data['service_type'],
            'provider_code' => $data['provider_code'],
            'product_code' => $data['product_code'] ?? null,
            'customer_reference' => $data['customer_reference'],
            'amount' => (float) $data['amount'],
            'currency' => $currency,
            'customer' => [
                'name' => $this->customerName($data, $user),
                'email' => $this->customerEmail($data, $user),
                'phone' => $data['customer_phone'] ?? $user->phone,
            ],
            'metadata' => $data['metadata'] ?? [],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    protected function providerPayloadFromTransaction(VtuTransaction $transaction): array
    {
        $payload = $transaction->request_payload;

        if (is_array($payload) && ! empty($payload)) {
            return $payload;
        }

        return [
            'reference' => $transaction->reference,
            'service_type' => $transaction->service_type,
            'provider_code' => $transaction->provider_code,
            'product_code' => $transaction->product_code,
            'customer_reference' => $transaction->customer_reference,
            'amount' => (float) $transaction->amount,
            'currency' => $transaction->currency,
            'customer' => [
                'name' => $transaction->customer_name,
                'email' => $transaction->customer_email,
                'phone' => $transaction->customer_phone,
            ],
            'metadata' => $transaction->metadata ?? [],
        ];
    }

    /**
     * @param  array<string,mixed>  $metadata
     * @return array<string,mixed>
     */
    protected function paymentMetadata(array $metadata, VtuTransaction $transaction): array
    {
        $metadata['_vtu'] = [
            'reference' => $transaction->reference,
            'service_type' => $transaction->service_type,
            'provider_code' => $transaction->provider_code,
            'product_code' => $transaction->product_code,
            'customer_reference' => $transaction->customer_reference,
        ];

        return $metadata;
    }

    /**
     * @param  array<string,mixed>  $metadata
     * @return array<string,mixed>
     */
    protected function monnifyMetadata(array $metadata, VtuTransaction $transaction): array
    {
        $monnifyMetadata = [
            'vtu_reference' => $transaction->reference,
            'vtu_service_type' => $transaction->service_type,
            'vtu_provider_code' => $transaction->provider_code,
            'vtu_customer_reference' => $transaction->customer_reference,
        ];

        if ($transaction->product_code) {
            $monnifyMetadata['vtu_product_code'] = $transaction->product_code;
        }

        foreach ($metadata as $key => $value) {
            if ($key === '_vtu' || $value === null) {
                continue;
            }

            if (is_scalar($value)) {
                $monnifyMetadata[(string) $key] = $value;
            }
        }

        return $monnifyMetadata;
    }

    protected function generateVtuReference(): string
    {
        do {
            $reference = 'VTU-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(8));
        } while (VtuTransaction::where('reference', $reference)->exists());

        return $reference;
    }

    protected function generatePaymentReference(): string
    {
        do {
            $reference = 'VTU-PAY-' . strtoupper(Str::random(10)) . '-' . now()->timestamp;
        } while (Transaction::where('payment_reference', $reference)->exists());

        return $reference;
    }

    protected function descriptionFor(string $serviceType, string $customerReference): string
    {
        return $this->serviceLabel($serviceType) . ' purchase for ' . $customerReference;
    }

    protected function serviceLabel(string $serviceType): string
    {
        return (string) config("vtu.services.{$serviceType}.label", Str::headline($serviceType));
    }

    /**
     * @param  array<string,mixed>  $data
     */
    protected function customerName(array $data, User $user): string
    {
        return (string) ($data['customer_name'] ?? $user->name ?? 'Customer');
    }

    /**
     * @param  array<string,mixed>  $data
     */
    protected function customerEmail(array $data, User $user): string
    {
        return (string) ($data['customer_email'] ?? $user->email ?? '');
    }
}
