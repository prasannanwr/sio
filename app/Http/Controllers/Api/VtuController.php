<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\VtuCustomerValidationException;
use App\Exceptions\VtuServiceUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\InitializeVtuPaymentRequest;
use App\Http\Requests\VtuCustomerValidationRequest;
use App\Http\Requests\VtuPurchaseRequest;
use App\Models\Transaction as PaymentTransaction;
use App\Models\VtuTransaction;
use App\Services\VtuPaymentService;
use App\Services\VtuProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class VtuController extends Controller
{
    protected VtuProviderService $provider;
    protected VtuPaymentService $payments;

    public function __construct(VtuProviderService $provider, VtuPaymentService $payments)
    {
        $this->provider = $provider;
        $this->payments = $payments;
    }

    public function dashboard(Request $request): JsonResponse
    {
        $baseQuery = VtuTransaction::query()
            ->where('user_id', $request->user()->id);

        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('COUNT(*) as transactions_count'))
            ->groupBy('status')
            ->pluck('transactions_count', 'status');

        $serviceBreakdown = (clone $baseQuery)
            ->select(
                'service_type',
                DB::raw('COUNT(*) as transactions_count'),
                DB::raw("SUM(CASE WHEN status = '" . VtuTransaction::STATUS_SUCCESSFUL . "' THEN amount ELSE 0 END) as successful_amount")
            )
            ->groupBy('service_type')
            ->get()
            ->map(function (VtuTransaction $row) {
                return [
                    'service_type' => $row->service_type,
                    'service_label' => $this->serviceLabel($row->service_type),
                    'transactions_count' => (int) $row->transactions_count,
                    'successful_amount' => number_format((float) $row->successful_amount, 2, '.', ''),
                ];
            })
            ->values();

        $recentTransactions = (clone $baseQuery)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (VtuTransaction $transaction) => $this->transactionPayload($transaction))
            ->values();

        return response()->json([
            'status' => true,
            'message' => 'VTU dashboard retrieved successfully.',
            'data' => [
                'summary' => [
                    'total_transactions' => (clone $baseQuery)->count(),
                    'successful_transactions' => (int) ($statusCounts[VtuTransaction::STATUS_SUCCESSFUL] ?? 0),
                    'failed_transactions' => (int) ($statusCounts[VtuTransaction::STATUS_FAILED] ?? 0),
                    'pending_transactions' => (int) ($statusCounts[VtuTransaction::STATUS_PENDING] ?? 0)
                        + (int) ($statusCounts[VtuTransaction::STATUS_PROCESSING] ?? 0),
                    'total_successful_amount' => number_format((float) (clone $baseQuery)
                        ->where('status', VtuTransaction::STATUS_SUCCESSFUL)
                        ->sum('amount'), 2, '.', ''),
                    'currency' => config('vtu.currency', 'NGN'),
                ],
                'services' => $this->provider->services($request->user()),
                'service_breakdown' => $serviceBreakdown,
                'recent_transactions' => $recentTransactions,
            ],
        ]);
    }

    public function services(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'VTU services retrieved successfully.',
            'data' => $this->provider->services($request->user()),
        ]);
    }

    public function providers(Request $request): JsonResponse
    {
        $query = $this->normalizedQuery($request);

        $validator = Validator::make($query, [
            'service_type' => ['required', 'string', Rule::in(array_keys(config('vtu.services', [])))],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        try {
            $this->provider->assertServiceAvailable($validator->validated()['service_type'], $request->user());
            $providers = $this->provider->providers($validator->validated()['service_type']);
        } catch (Throwable $e) {
            return $this->providerError($e);
        }

        return response()->json([
            'status' => true,
            'message' => 'VTU providers retrieved successfully.',
            'data' => $providers,
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $query = $this->normalizedQuery($request);

        $validator = Validator::make($query, [
            'service_type' => ['required', 'string', Rule::in(array_keys(config('vtu.services', [])))],
            'provider_code' => ['required', 'string', 'max:80'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $data = $validator->validated();

        try {
            $this->provider->assertServiceAvailable($data['service_type'], $request->user());
            $products = $this->provider->products($data['service_type'], $data['provider_code']);
        } catch (Throwable $e) {
            return $this->providerError($e);
        }

        return response()->json([
            'status' => true,
            'message' => 'VTU products retrieved successfully.',
            'data' => $products,
        ]);
    }

    public function validateCustomer(VtuCustomerValidationRequest $request): JsonResponse
    {
        try {
            $this->provider->assertServiceAvailable($request->validated()['service_type'], $request->user());
            $response = $this->provider->validateCustomer($request->validated());
        } catch (Throwable $e) {
            return $this->providerError($e);
        }

        return response()->json([
            'status' => true,
            'message' => 'Customer validation completed.',
            'data' => $response,
        ]);
    }

    public function initializePayment(InitializeVtuPaymentRequest $request): JsonResponse
    {
        try {
            $this->provider->assertServiceAvailable($request->validated()['service_type'], $request->user());
            $result = $this->payments->initializePayment($request->validated(), $request->user());
        } catch (VtuServiceUnavailableException | VtuCustomerValidationException $e) {
            return $this->providerError($e);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not initialize VTU payment.',
                'error' => $e->getMessage(),
            ], 502);
        }

        $paymentPayload = $this->paymentPayload($result['payment']);

        return response()->json([
            'status' => true,
            'message' => 'VTU payment initialized successfully.',
            // SDK fields are also surfaced at the top level (matching /api/payments/initialize)
            // so the app can read data.sdk_payload / data.checkout_url consistently.
            'data' => [
                'payment_reference' => $paymentPayload['payment_reference'],
                'checkout_url' => $paymentPayload['checkout_url'],
                'amount' => $paymentPayload['amount'],
                'currency' => $paymentPayload['currency'],
                'sdk_credentials' => $paymentPayload['sdk_credentials'],
                'sdk_payload' => $paymentPayload['sdk_payload'],
                'transaction' => $this->transactionPayload($result['vtu_transaction']),
                'payment' => $paymentPayload,
            ],
        ], 201);
    }

    public function purchase(VtuPurchaseRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $this->provider->assertServiceAvailable($data['service_type'], $request->user());
        } catch (VtuServiceUnavailableException $e) {
            return $this->providerError($e);
        }

        if ($paymentError = $this->validatePaymentReference($data, $request)) {
            return $paymentError;
        }

        $reference = $this->generateReference();
        $currency = $data['currency'] ?? config('vtu.currency', 'NGN');

        $providerPayload = [
            'reference' => $reference,
            'service_type' => $data['service_type'],
            'provider_code' => $data['provider_code'],
            'product_code' => $data['product_code'] ?? null,
            'customer_reference' => $data['customer_reference'],
            'amount' => (float) $data['amount'],
            'currency' => $currency,
            'customer' => [
                'name' => $data['customer_name'] ?? $request->user()->name,
                'email' => $data['customer_email'] ?? $request->user()->email,
                'phone' => $data['customer_phone'] ?? $request->user()->phone,
            ],
            'metadata' => $data['metadata'] ?? [],
        ];

        $transaction = VtuTransaction::create([
            'user_id' => $request->user()->id,
            'reference' => $reference,
            'payment_reference' => $data['payment_reference'] ?? null,
            'service_type' => $data['service_type'],
            'provider_code' => $data['provider_code'],
            'product_code' => $data['product_code'] ?? null,
            'customer_reference' => $data['customer_reference'],
            'customer_name' => $data['customer_name'] ?? $request->user()->name,
            'customer_email' => $data['customer_email'] ?? $request->user()->email,
            'customer_phone' => $data['customer_phone'] ?? $request->user()->phone,
            'amount' => $data['amount'],
            'currency' => $currency,
            'status' => VtuTransaction::STATUS_PENDING,
            'description' => $this->descriptionFor($data['service_type'], $data['customer_reference']),
            'metadata' => $data['metadata'] ?? null,
            'request_payload' => $providerPayload,
        ]);

        try {
            $providerResponse = $this->provider->purchase($providerPayload);
        } catch (Throwable $e) {
            $transaction->update([
                'status' => VtuTransaction::STATUS_FAILED,
                'provider_response' => ['error' => $e->getMessage()],
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Could not complete VTU purchase.',
                'error' => $e->getMessage(),
                'data' => [
                    'transaction' => $this->transactionPayload($transaction->fresh()),
                ],
            ], 502);
        }

        $this->syncTransactionFromProvider($transaction, $providerResponse);

        return response()->json([
            'status' => true,
            'message' => 'VTU transaction created successfully.',
            'data' => [
                'transaction' => $this->transactionPayload($transaction->fresh()),
                'provider_response' => $providerResponse,
            ],
        ], 201);
    }

    public function transactions(Request $request): JsonResponse
    {
        $queryParams = $this->normalizedQuery($request);

        $validator = Validator::make($queryParams, [
            'service_type' => ['nullable', 'string', Rule::in(array_keys(config('vtu.services', [])))],
            'status' => ['nullable', 'string', Rule::in([
                VtuTransaction::STATUS_PENDING,
                VtuTransaction::STATUS_PROCESSING,
                VtuTransaction::STATUS_SUCCESSFUL,
                VtuTransaction::STATUS_FAILED,
                VtuTransaction::STATUS_REVERSED,
            ])],
            'search' => ['nullable', 'string', 'max:120'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $filters = $validator->validated();
        $query = VtuTransaction::query()
            ->where('user_id', $request->user()->id)
            ->latest();

        if (! empty($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhere('provider_reference', 'like', "%{$search}%")
                    ->orWhere('customer_reference', 'like', "%{$search}%");
            });
        }

        $perPage = (int) ($filters['per_page'] ?? 15);
        $transactions = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'VTU transactions retrieved successfully.',
            'total' => $transactions->total(),
            'page' => $transactions->currentPage(),
            'page_size' => $transactions->perPage(),
            'data' => $transactions->getCollection()
                ->map(fn (VtuTransaction $transaction) => $this->transactionPayload($transaction))
                ->values(),
        ]);
    }

    public function show(Request $request, string $reference): JsonResponse
    {
        $transaction = VtuTransaction::query()
            ->where('user_id', $request->user()->id)
            ->where('reference', $reference)
            ->first();

        if (! $transaction) {
            return response()->json([
                'status' => false,
                'message' => 'VTU transaction not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'transaction' => $this->transactionPayload($transaction),
            ],
        ]);
    }

    public function refresh(Request $request, string $reference): JsonResponse
    {
        $transaction = VtuTransaction::query()
            ->where('user_id', $request->user()->id)
            ->where('reference', $reference)
            ->first();

        if (! $transaction) {
            return response()->json([
                'status' => false,
                'message' => 'VTU transaction not found.',
            ], 404);
        }

        // Nothing is sent to the provider until the payment is confirmed.
        if ($transaction->status === VtuTransaction::STATUS_PENDING) {
            return response()->json([
                'status' => true,
                'message' => 'VTU transaction is awaiting payment.',
                'data' => [
                    'transaction' => $this->transactionPayload($transaction),
                ],
            ]);
        }

        try {
            $providerResponse = $this->provider->status($transaction->reference);
        } catch (Throwable $e) {
            return $this->providerError($e);
        }

        $this->syncTransactionFromProvider($transaction, $providerResponse);

        return response()->json([
            'status' => true,
            'message' => 'VTU transaction refreshed successfully.',
            'data' => [
                'transaction' => $this->transactionPayload($transaction->fresh()),
                'provider_response' => $providerResponse,
            ],
        ]);
    }

    protected function syncTransactionFromProvider(VtuTransaction $transaction, array $providerResponse): void
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

    protected function validatePaymentReference(array $data, Request $request): ?JsonResponse
    {
        if (empty($data['payment_reference'])) {
            if (config('vtu.require_successful_payment', false)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment reference is required before VTU fulfillment.',
                ], 422);
            }

            return null;
        }

        $payment = PaymentTransaction::query()
            ->where('payment_reference', $data['payment_reference'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $payment) {
            return response()->json([
                'status' => false,
                'message' => 'Payment reference was not found for this user.',
            ], 422);
        }

        if (! config('vtu.require_successful_payment', false)) {
            return null;
        }

        if (! $payment->isSuccessful()) {
            return response()->json([
                'status' => false,
                'message' => 'Payment must be successful before VTU fulfillment.',
            ], 422);
        }

        $paidAmount = (float) ($payment->amount_paid ?: $payment->amount);

        if ($paidAmount < (float) $data['amount']) {
            return response()->json([
                'status' => false,
                'message' => 'Payment amount is lower than the VTU transaction amount.',
            ], 422);
        }

        return null;
    }

    protected function paymentPayload(PaymentTransaction $payment): array
    {
        return [
            'payment_reference' => $payment->payment_reference,
            'transaction_reference' => $payment->transaction_reference,
            'checkout_url' => $payment->checkout_url,
            'amount' => $payment->amount,
            'amount_paid' => $payment->amount_paid,
            'currency' => $payment->currency,
            'status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'paid_at' => optional($payment->paid_at)->toIso8601String(),
            'is_successful' => $payment->isSuccessful(),
            'sdk_credentials' => [
                'api_key' => config('monnify.api_key'),
                'contract_code' => config('monnify.contract_code'),
            ],
            'sdk_payload' => [
                'apiKey' => config('monnify.api_key'),
                'contractCode' => config('monnify.contract_code'),
                'amount' => (float) $payment->amount,
                'currencyCode' => $payment->currency,
                'paymentReference' => $payment->payment_reference,
                'paymentDescription' => $payment->payment_description,
                'customerName' => $payment->customer_name,
                'customerEmail' => $payment->customer_email,
                'customerPhone' => $payment->customer_phone,
            ],
        ];
    }

    protected function transactionPayload(VtuTransaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'reference' => $transaction->reference,
            'payment_reference' => $transaction->payment_reference,
            'provider_reference' => $transaction->provider_reference,
            'service_type' => $transaction->service_type,
            'service_label' => $this->serviceLabel($transaction->service_type),
            'provider_code' => $transaction->provider_code,
            'provider_name' => $this->providerName($transaction->service_type, $transaction->provider_code),
            'product_code' => $transaction->product_code,
            'customer_reference' => $transaction->customer_reference,
            'customer_name' => $transaction->customer_name,
            'customer_email' => $transaction->customer_email,
            'customer_phone' => $transaction->customer_phone,
            'amount' => $transaction->amount,
            'fee' => $transaction->fee,
            'currency' => $transaction->currency,
            'status' => $transaction->status,
            'description' => $transaction->description,
            'metadata' => $transaction->metadata,
            'is_successful' => $transaction->isSuccessful(),
            'purchase_details' => $transaction->purchaseDetails(),
            'fulfilled_at' => optional($transaction->fulfilled_at)->toIso8601String(),
            'created_at' => optional($transaction->created_at)->toIso8601String(),
            'updated_at' => optional($transaction->updated_at)->toIso8601String(),
        ];
    }

    protected function generateReference(): string
    {
        do {
            $reference = 'VTU-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(8));
        } while (VtuTransaction::where('reference', $reference)->exists());

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

    protected function providerName(string $serviceType, string $providerCode): string
    {
        foreach (config("vtu.providers.{$serviceType}", []) as $provider) {
            if (($provider['code'] ?? null) === $providerCode) {
                return $provider['name'] ?? $providerCode;
            }
        }

        return Str::headline(str_replace(['-', '_'], ' ', $providerCode));
    }

    protected function normalizedQuery(Request $request): array
    {
        $query = $request->query();

        if (isset($query['service_type'])) {
            $query['service_type'] = strtolower((string) $query['service_type']);
        }

        if (isset($query['status'])) {
            $query['status'] = strtoupper((string) $query['status']);
        }

        return $query;
    }

    protected function validationResponse($validator): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    protected function providerError(Throwable $e): JsonResponse
    {
        // Problems the customer can act on get their own message instead of a generic failure.
        if ($e instanceof VtuServiceUnavailableException || $e instanceof VtuCustomerValidationException) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], $e instanceof VtuServiceUnavailableException ? 503 : 422);
        }

        return response()->json([
            'status' => false,
            'message' => 'VTU provider request failed.',
            'error' => $e->getMessage(),
        ], 502);
    }
}
