<?php

namespace App\Services;

use App\Exceptions\MonnifyBillsException;
use App\Exceptions\VtuCustomerValidationException;
use App\Exceptions\VtuServiceUnavailableException;
use App\Models\VtuTransaction;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class VtuProviderService
{
    public function __construct(protected MonnifyService $monnify)
    {
    }

    public function services(?Authenticatable $user = null): array
    {
        $services = [];

        foreach (config('vtu.services', []) as $code => $service) {
            $services[] = array_merge(['code' => $code], $service, [
                'available' => $this->isServiceAvailable($code, $user),
            ]);
        }

        return $services;
    }

    /**
     * Whether a user may start a purchase of this service. With the Monnify driver, services
     * not in VTU_MONNIFY_SERVICES are open only to VTU_MONNIFY_TEST_USERS. This is checked
     * when a purchase starts; one already paid for is completed either way.
     */
    public function isServiceAvailable(string $serviceType, ?Authenticatable $user = null): bool
    {
        if (! $this->usesMonnifyDriver()) {
            return true;
        }

        if (! config("vtu.monnify.categories.{$serviceType}")) {
            return false;
        }

        if (in_array($serviceType, (array) config('vtu.monnify.services', []), true)) {
            return true;
        }

        return $user !== null
            && in_array((int) $user->getAuthIdentifier(), (array) config('vtu.monnify.test_users', []), true);
    }

    public function assertServiceAvailable(string $serviceType, ?Authenticatable $user = null): void
    {
        if (! $this->isServiceAvailable($serviceType, $user)) {
            throw $this->serviceUnavailable($serviceType);
        }
    }

    public function providers(string $serviceType): array
    {
        if ($this->usesMockDriver()) {
            return config("vtu.providers.{$serviceType}", []);
        }

        if ($this->usesMonnifyDriver()) {
            return $this->monnifyProviders($serviceType);
        }

        return $this->request('providers', ['service_type' => $serviceType]);
    }

    public function products(string $serviceType, string $providerCode): array
    {
        if ($this->usesMockDriver()) {
            return config("vtu.products.{$serviceType}.{$providerCode}", []);
        }

        if ($this->usesMonnifyDriver()) {
            return $this->monnifyProducts($serviceType, $providerCode);
        }

        return $this->request('products', [
            'service_type' => $serviceType,
            'provider_code' => $providerCode,
        ]);
    }

    public function validateCustomer(array $payload): array
    {
        if ($this->usesMockDriver()) {
            return [
                'status' => true,
                'message' => 'Customer validated successfully.',
                'data' => [
                    'customer_reference' => $payload['customer_reference'],
                    'customer_name' => $payload['customer_name'] ?? 'Demo Customer',
                    'service_type' => $payload['service_type'],
                    'provider_code' => $payload['provider_code'],
                ],
            ];
        }

        if ($this->usesMonnifyDriver()) {
            return $this->monnifyValidateCustomer($payload);
        }

        return $this->request('validate_customer', $payload);
    }

    /**
     * Checks to run before the customer is charged, so that a purchase the provider
     * would refuse (unknown network, invalid number, wrong amount) never takes money.
     */
    public function preflight(array $payload): void
    {
        if (! $this->usesMonnifyDriver()) {
            return;
        }

        $product = $this->monnifyProduct($payload['service_type'], $payload['provider_code'], $payload['product_code'] ?? null);

        $this->assertMonnifyAmount($product, (float) $payload['amount']);

        if (config('vtu.monnify.validate_before_payment', true)) {
            $this->monnifyValidation($product, $this->monnifyCustomerId($payload['service_type'], $payload['customer_reference']));
        }
    }

    public function purchase(array $payload): array
    {
        if ($this->usesMockDriver()) {
            return [
                'status' => 'successful',
                'message' => 'VTU purchase completed in mock mode.',
                'reference' => $payload['reference'],
                'provider_reference' => 'MOCK-' . strtoupper(Str::random(12)),
                'data' => [
                    'service_type' => $payload['service_type'],
                    'provider_code' => $payload['provider_code'],
                    'product_code' => $payload['product_code'] ?? null,
                    'customer_reference' => $payload['customer_reference'],
                    'amount' => $payload['amount'],
                ],
            ];
        }

        if ($this->usesMonnifyDriver()) {
            return $this->monnifyPurchase($payload);
        }

        return $this->request('purchase', $payload);
    }

    public function status(string $reference): array
    {
        if ($this->usesMockDriver()) {
            return [
                'status' => 'successful',
                'message' => 'Mock transactions complete during purchase.',
                'reference' => $reference,
            ];
        }

        if ($this->usesMonnifyDriver()) {
            return $this->monnifyStatus($reference);
        }

        return $this->request('status', ['reference' => $reference]);
    }

    public function normalizeStatus(array $response): string
    {
        $statusValue = (
            data_get($response, 'status')
            ?? data_get($response, 'transaction_status')
            ?? data_get($response, 'transactionStatus')
            ?? data_get($response, 'data.status')
            ?? data_get($response, 'responseBody.status')
            ?? data_get($response, 'responseBody.transactionStatus')
            ?? ''
        );
        $status = strtolower((string) $statusValue);

        if ($statusValue === false) {
            return VtuTransaction::STATUS_FAILED;
        }

        if (in_array($status, ['success', 'successful', 'completed', 'complete', 'delivered', 'fulfilled'], true)) {
            return VtuTransaction::STATUS_SUCCESSFUL;
        }

        if (in_array($status, ['failed', 'failure', 'declined', 'cancelled', 'canceled', 'error'], true)) {
            return VtuTransaction::STATUS_FAILED;
        }

        if (in_array($status, ['reversed', 'refunded', 'refund'], true)) {
            return VtuTransaction::STATUS_REVERSED;
        }

        if (in_array($status, ['pending', 'processing', 'in-progress', 'in_progress', 'queued'], true)) {
            return VtuTransaction::STATUS_PROCESSING;
        }

        if (data_get($response, 'success') === false
            || data_get($response, 'requestSuccessful') === false
            || data_get($response, 'data.success') === false
            || data_get($response, 'responseBody.success') === false) {
            return VtuTransaction::STATUS_FAILED;
        }

        if ((bool) data_get($response, 'success')
            || (bool) data_get($response, 'requestSuccessful')
            || (bool) data_get($response, 'data.success')
            || (bool) data_get($response, 'responseBody.success')) {
            return VtuTransaction::STATUS_PROCESSING;
        }

        return VtuTransaction::STATUS_PROCESSING;
    }

    public function providerReference(array $response): ?string
    {
        return data_get($response, 'provider_reference')
            ?? data_get($response, 'providerReference')
            ?? data_get($response, 'transaction_reference')
            ?? data_get($response, 'transactionReference')
            ?? data_get($response, 'reference')
            ?? data_get($response, 'data.provider_reference')
            ?? data_get($response, 'data.reference')
            ?? data_get($response, 'responseBody.providerReference')
            ?? data_get($response, 'responseBody.reference');
    }

    // ---------------------------------------------------------------------
    // Monnify Bills Payment driver
    // ---------------------------------------------------------------------

    /**
     * Provider lists come from config, not Monnify: Monnify ignores the category filter
     * and returns its whole catalogue (over 5,000 billers), so listing it is slow and
     * would show billers the app does not offer.
     */
    protected function monnifyProviders(string $serviceType): array
    {
        $this->monnifyCategory($serviceType);

        $providers = [];

        foreach ($this->configuredBillers($serviceType) as $code => $biller) {
            $providers[] = ['code' => (string) $code, 'name' => (string) ($biller['name'] ?? $code)];
        }

        return $providers;
    }

    protected function monnifyProducts(string $serviceType, string $providerCode): array
    {
        return array_map(function (array $product) {
            $isVariable = strtoupper((string) ($product['priceType'] ?? '')) === 'OPEN' || ! isset($product['price']);

            return [
                'code' => (string) $product['code'],
                'name' => (string) ($product['name'] ?? $product['code']),
                // amount and is_variable_amount match the mock products the app was built against.
                'amount' => $isVariable ? null : $product['price'],
                'is_variable_amount' => $isVariable,
                'price' => $product['price'] ?? null,
                'price_type' => $product['priceType'] ?? null,
                'min_amount' => $product['minAmount'] ?? null,
                'max_amount' => $product['maxAmount'] ?? null,
            ];
        }, $this->monnifyBillerProducts($serviceType, $providerCode));
    }

    protected function monnifyValidateCustomer(array $payload): array
    {
        $product = $this->monnifyProduct($payload['service_type'], $payload['provider_code'], $payload['product_code'] ?? null);
        $customerId = $this->monnifyCustomerId($payload['service_type'], $payload['customer_reference']);
        $validation = $this->monnifyValidation($product, $customerId);

        return [
            'status' => true,
            'message' => 'Customer validated successfully.',
            'data' => [
                'customer_reference' => $customerId,
                'customer_name' => $this->firstValue($validation, ['customerName', 'customer.name', 'name']),
                'service_type' => $payload['service_type'],
                'provider_code' => $payload['provider_code'],
                'product_code' => (string) $product['code'],
            ],
        ];
    }

    protected function monnifyPurchase(array $payload): array
    {
        $reference = (string) $payload['reference'];
        $product = $this->monnifyProduct($payload['service_type'], $payload['provider_code'], $payload['product_code'] ?? null);
        $customerId = $this->monnifyCustomerId($payload['service_type'], $payload['customer_reference']);
        $amount = (float) $payload['amount'];

        $this->assertMonnifyAmount($product, $amount);

        $validation = $this->monnifyValidation($product, $customerId);

        $vend = [
            'productCode' => (string) $product['code'],
            'customerId' => $customerId,
            'vendAmount' => $amount,
            'vendReference' => $reference,
        ];

        if (data_get($validation, 'vendInstruction.requireValidationRef')) {
            $vend['validationReference'] = $validation['validationReference'] ?? null;
        }

        try {
            return $this->monnifyResult($this->monnify->vendBill($vend), $reference);
        } catch (ConnectionException $e) {
            return $this->monnifyUnconfirmedVend($reference, $e);
        } catch (MonnifyBillsException $e) {
            // A 4xx is a definite rejection: nothing was bought, so the purchase fails.
            if (! $e->outcomeUnknown()) {
                throw $e;
            }

            return $this->monnifyUnconfirmedVend($reference, $e);
        }
    }

    /**
     * The vend may or may not have gone through. Report it as processing unless
     * Monnify already has a result: marking it failed could lead to a second
     * vend for airtime that was delivered. vtu:reconcile settles it later.
     */
    protected function monnifyUnconfirmedVend(string $reference, Throwable $e): array
    {
        Log::warning('Monnify vend outcome unknown, leaving it processing', [
            'vend_reference' => $reference,
            'error' => $e->getMessage(),
        ]);

        try {
            $requery = $this->monnify->requeryBill($reference);

            if (is_array($requery)) {
                return $this->monnifyResult($requery, $reference);
            }
        } catch (Throwable $requeryError) {
            // Still unknown; the reconciliation job asks again.
        }

        return [
            'status' => 'processing',
            'message' => 'Waiting for Monnify to confirm the purchase.',
            'reference' => $reference,
        ];
    }

    protected function monnifyStatus(string $reference): array
    {
        $result = $this->monnify->requeryBill($reference);

        if ($result === null) {
            return [
                'status' => 'not_found',
                'message' => 'Monnify has no purchase with this reference.',
                'reference' => $reference,
            ];
        }

        return $this->monnifyResult($result, $reference);
    }

    protected function monnifyResult(array $body, string $reference): array
    {
        $result = [
            // SUCCESS, IN_PROGRESS or FAILED, which normalizeStatus() maps.
            'status' => strtolower((string) ($body['vendStatus'] ?? 'processing')),
            'reference' => $body['vendReference'] ?? $reference,
            'provider_reference' => $body['transactionReference'] ?? null,
        ];

        // What the customer needs from the purchase, such as an electricity token or exam PINs.
        // Monnify does not document these field names, so the likely ones are checked.
        $details = [
            'token' => $this->firstValue($body, ['token', 'tokenValue', 'electricityToken', 'meterToken']),
            'units' => $this->firstValue($body, ['units', 'unitsPurchased', 'kwh']),
            'pins' => $this->firstValue($body, ['pins', 'pin', 'pinDetails', 'cards']),
        ];

        return $result + array_filter($details, fn ($value) => $value !== null) + ['data' => $body];
    }

    protected function monnifyCategory(string $serviceType): string
    {
        $category = config("vtu.monnify.categories.{$serviceType}");

        if (! $category) {
            throw $this->serviceUnavailable($serviceType);
        }

        return (string) $category;
    }

    protected function serviceUnavailable(string $serviceType): VtuServiceUnavailableException
    {
        $label = config("vtu.services.{$serviceType}.label", Str::headline($serviceType));

        return new VtuServiceUnavailableException("{$label} purchases are not available yet.");
    }

    protected function monnifyBillerProducts(string $serviceType, string $providerCode): array
    {
        $category = $this->monnifyCategory($serviceType);
        $billerCode = $this->monnifyBillerCode($serviceType, $providerCode);

        // Monnify returns its whole product catalogue whichever biller is asked for,
        // so it is fetched once and shared by every biller.
        $catalogue = $this->rememberNonEmpty('vtu.monnify.products', fn () => $this->monnify->billerProducts());

        return array_values(array_filter($catalogue, function (array $product) use ($billerCode, $category) {
            return strcasecmp((string) data_get($product, 'biller.code'), $billerCode) === 0
                && strcasecmp((string) data_get($product, 'category.code'), $category) === 0;
        }));
    }

    protected function monnifyBillerCode(string $serviceType, string $providerCode): string
    {
        $code = $this->configuredBillers($serviceType)[$providerCode]['code'] ?? null;

        if (! $code) {
            throw new VtuCustomerValidationException("The provider [{$providerCode}] is not available.");
        }

        return (string) $code;
    }

    /**
     * The billers offered for a service, keyed by the app's provider code.
     */
    protected function configuredBillers(string $serviceType): array
    {
        return (array) config("vtu.monnify.billers.{$serviceType}", []);
    }

    /**
     * The Monnify product to buy. Airtime has no product choice in the app, so it uses
     * the network's open-amount "Mobile Top up" product unless one is pinned in config.
     */
    protected function monnifyProduct(string $serviceType, string $providerCode, ?string $productCode): array
    {
        $products = $this->monnifyBillerProducts($serviceType, $providerCode);
        $productCode = $productCode
            ?: config("vtu.monnify.billers.{$serviceType}.{$providerCode}.product")
            ?: config("vtu.monnify.products.{$serviceType}." . strtolower($providerCode));

        if ($productCode) {
            foreach ($products as $product) {
                if ((string) $product['code'] === (string) $productCode) {
                    return $product;
                }
            }

            throw new VtuCustomerValidationException("The product [{$productCode}] is not available for {$providerCode}.");
        }

        $open = array_values(array_filter($products, fn (array $product) => strtoupper((string) ($product['priceType'] ?? '')) === 'OPEN'));
        $topUps = array_values(array_filter($open, fn (array $product) => preg_match('/top\s*-?\s*up/i', (string) ($product['name'] ?? ''))));

        if (count($topUps) === 1) {
            return $topUps[0];
        }

        if (count($topUps) === 0 && count($open) === 1) {
            return $open[0];
        }

        if ($open === []) {
            throw new VtuServiceUnavailableException("{$this->providerDisplayName($serviceType, $providerCode)} is not available right now.");
        }

        // Several candidates: never guess which product the customer is paying for.
        throw new RuntimeException("Could not choose a Monnify {$serviceType} product for {$providerCode}. Set 'product' for it in vtu.monnify.billers.{$serviceType}.");
    }

    protected function assertMonnifyAmount(array $product, float $amount): void
    {
        $name = $product['name'] ?? $product['code'];

        if (strtoupper((string) ($product['priceType'] ?? '')) === 'FIXED'
            && isset($product['price'])
            && abs((float) $product['price'] - $amount) > 0.001) {
            throw new VtuCustomerValidationException("{$name} costs {$product['price']}. The amount must match.");
        }

        if (isset($product['minAmount']) && $amount < (float) $product['minAmount']) {
            throw new VtuCustomerValidationException("The minimum amount for {$name} is {$product['minAmount']}.");
        }

        if (isset($product['maxAmount']) && $amount > (float) $product['maxAmount']) {
            throw new VtuCustomerValidationException("The maximum amount for {$name} is {$product['maxAmount']}.");
        }
    }

    protected function monnifyValidation(array $product, string $customerId): array
    {
        try {
            return $this->monnify->validateBillCustomer((string) $product['code'], $customerId);
        } catch (MonnifyBillsException $e) {
            if ($e->outcomeUnknown()) {
                throw $e;
            }

            throw new VtuCustomerValidationException("{$customerId} could not be validated: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Phone-based services go to Monnify in the local 11-digit format (080...).
     */
    protected function monnifyCustomerId(string $serviceType, string $customerReference): string
    {
        $customerReference = trim($customerReference);

        if (! in_array($serviceType, ['airtime', 'data'], true)) {
            return $customerReference;
        }

        $digits = preg_replace('/\D+/', '', $customerReference);

        if (strlen($digits) === 13 && str_starts_with($digits, '234')) {
            return '0' . substr($digits, 3);
        }

        if (strlen($digits) === 10) {
            return '0' . $digits;
        }

        return $digits;
    }

    /**
     * The first non-empty value among field names that differ between Monnify billers.
     */
    protected function firstValue(array $data, array $keys)
    {
        foreach ($keys as $key) {
            $value = data_get($data, $key);

            if ($value !== null && $value !== '' && $value !== []) {
                return $value;
            }
        }

        return null;
    }

    protected function providerDisplayName(string $serviceType, string $providerCode): string
    {
        return $this->configuredBillers($serviceType)[$providerCode]['name']
            ?? $this->configuredProviderName($serviceType, strtolower($providerCode))
            ?? $providerCode;
    }

    protected function configuredProviderName(string $serviceType, string $code): ?string
    {
        foreach (config("vtu.providers.{$serviceType}", []) as $provider) {
            if (($provider['code'] ?? null) === $code) {
                return $provider['name'] ?? null;
            }
        }

        return null;
    }

    /**
     * Cache a Monnify listing, but never an empty one: an outage must not hide
     * every network until the cache expires.
     */
    protected function rememberNonEmpty(string $key, callable $callback): array
    {
        $cached = Cache::get($key);

        if (is_array($cached) && $cached !== []) {
            return $cached;
        }

        $value = $callback();

        if ($value !== []) {
            Cache::put($key, $value, now()->addMinutes((int) config('vtu.monnify.cache_minutes', 60)));
        }

        return $value;
    }

    protected function request(string $endpointKey, array $payload = []): array
    {
        $endpoint = config("vtu.provider.endpoints.{$endpointKey}");

        if (! is_array($endpoint) || empty($endpoint['path'])) {
            throw new RuntimeException("VTU provider endpoint [{$endpointKey}] is not configured.");
        }

        $baseUrl = rtrim((string) config('vtu.provider.base_url'), '/');

        if ($baseUrl === '') {
            throw new RuntimeException('VTU provider base URL is not configured.');
        }

        $method = strtoupper((string) Arr::get($endpoint, 'method', 'GET'));
        $path = $this->replacePathParameters((string) $endpoint['path'], $payload);
        $url = $baseUrl . '/' . ltrim($path, '/');
        $body = $this->payloadWithoutPathParameters($payload, (string) $endpoint['path']);

        $request = Http::timeout((int) config('vtu.provider.timeout', 30))
            ->acceptJson()
            ->withHeaders($this->headers());

        $response = $method === 'GET'
            ? $request->get($url, $body)
            : $request->send($method, $url, ['json' => $body]);

        $data = $response->json();

        if (! is_array($data)) {
            $data = ['raw_response' => $response->body()];
        }

        if (! $response->successful()) {
            Log::warning('VTU provider request failed', [
                'endpoint' => $endpointKey,
                'status' => $response->status(),
                'response' => $data,
            ]);

            throw new RuntimeException(
                data_get($data, 'message', data_get($data, 'responseMessage', 'VTU provider request failed')),
                $response->status()
            );
        }

        return $data;
    }

    protected function usesMockDriver(): bool
    {
        return strtolower((string) config('vtu.provider.driver', 'mock')) === 'mock';
    }

    protected function usesMonnifyDriver(): bool
    {
        return strtolower((string) config('vtu.provider.driver', 'mock')) === 'monnify';
    }

    protected function headers(): array
    {
        $headers = [];
        $apiKey = (string) config('vtu.provider.api_key');

        if ($apiKey !== '') {
            $apiKeyHeader = (string) config('vtu.provider.api_key_header', 'Authorization');

            $headers[$apiKeyHeader] = strtolower($apiKeyHeader) === 'authorization'
                ? trim(config('vtu.provider.auth_scheme', 'Bearer') . ' ' . $apiKey)
                : $apiKey;
        }

        $secretKey = (string) config('vtu.provider.secret_key');

        if ($secretKey !== '') {
            $headers['X-Secret-Key'] = $secretKey;
        }

        return $headers;
    }

    protected function replacePathParameters(string $path, array $payload): string
    {
        return preg_replace_callback('/\{([A-Za-z0-9_]+)\}/', function ($matches) use ($payload) {
            return rawurlencode((string) data_get($payload, $matches[1], ''));
        }, $path);
    }

    protected function payloadWithoutPathParameters(array $payload, string $path): array
    {
        preg_match_all('/\{([A-Za-z0-9_]+)\}/', $path, $matches);

        foreach ($matches[1] ?? [] as $key) {
            unset($payload[$key]);
        }

        return $payload;
    }
}
