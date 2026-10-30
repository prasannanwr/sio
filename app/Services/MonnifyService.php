<?php

namespace App\Services;

use App\Exceptions\MonnifyBillsException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin wrapper around the Monnify REST API.
 *
 * Docs: https://developers.monnify.com/api
 */
class MonnifyService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $secretKey;
    protected string $contractCode;

    public function __construct()
    {
        $this->baseUrl      = rtrim(trim((string) config('monnify.base_url')), '/');
        $this->apiKey       = trim((string) config('monnify.api_key'));
        $this->secretKey    = trim((string) config('monnify.secret_key'));
        $this->contractCode = trim((string) config('monnify.contract_code'));
    }

    /**
     * Get a Bearer access token. Tokens last ~1 hour, so we cache for 50 minutes.
     */
    public function getAccessToken(bool $forceRefresh = false): string
    {
        $this->ensureConfigured();

        $cacheKey = 'monnify.access_token';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(50), function () {
            $credentials = base64_encode("{$this->apiKey}:{$this->secretKey}");

            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
                'Content-Type'  => 'application/json',
            ])->post("{$this->baseUrl}/api/v1/auth/login");

            if (! $response->successful() || ! $response->json('requestSuccessful')) {
                $responseCode = $response->json('responseCode') ?: $response->status();
                $responseMessage = trim((string) $response->json('responseMessage'));

                Log::error('Monnify auth failed', [
                    'base_url' => $this->baseUrl,
                    'status' => $response->status(),
                    'response_code' => $responseCode,
                    'response_message' => $responseMessage,
                    'response' => $response->body(),
                ]);

                $details = $responseMessage !== ''
                    ? $responseMessage
                    : 'responseCode ' . $responseCode;

                throw new RuntimeException(
                    'Could not authenticate with Monnify (' . $details . '). '
                    . 'Check MONNIFY_BASE_URL, MONNIFY_API_KEY, and MONNIFY_SECRET_KEY belong to the same Monnify environment.'
                );
            }

            return $response->json('responseBody.accessToken');
        });
    }

    /**
     * Initialize a transaction. Returns the checkout URL + transaction reference.
     *
     * @param  array{
     *   amount: float|int,
     *   customerName: string,
     *   customerEmail: string,
     *   paymentReference: string,
     *   paymentDescription?: string,
     *   currencyCode?: string,
     *   redirectUrl?: string,
     *   paymentMethods?: array<string>,
     *   metadata?: array<string,mixed>,
     * } $payload
     */
    public function initializeTransaction(array $payload): array
    {
        $body = [
            'amount'             => $payload['amount'],
            'customerName'       => $payload['customerName'],
            'customerEmail'      => $payload['customerEmail'],
            'paymentReference'   => $payload['paymentReference'],
            'paymentDescription' => $payload['paymentDescription'] ?? 'Payment',
            'currencyCode'       => $payload['currencyCode'] ?? config('monnify.currency'),
            'contractCode'       => $this->contractCode,
            'redirectUrl'        => $payload['redirectUrl'] ?? config('monnify.redirect_url'),
        ];

        $paymentMethods = $this->paymentMethods($payload['paymentMethods'] ?? null);

        if (! empty($paymentMethods)) {
            $body['paymentMethods'] = $paymentMethods;
        }

        if (! empty($payload['metadata']) && is_array($payload['metadata'])) {
            $metadata = $this->metadata($payload['metadata']);

            if (! empty($metadata)) {
                $body['metaData'] = $metadata;
            }
        }

        return $this->request('POST', '/api/v1/merchant/transactions/init-transaction', $body);
    }

    /**
     * Monnify treats paymentMethods as optional and defaults to the available
     * methods on the merchant account, so only send it when the caller chooses.
     *
     * @param  array<string>|null  $paymentMethods
     * @return array<int,string>
     */
    protected function paymentMethods(?array $paymentMethods): array
    {
        if (empty($paymentMethods)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(function ($method) {
            return is_string($method) ? trim($method) : null;
        }, $paymentMethods))));
    }

    /**
     * Keep Monnify metadata flat. Nested arrays can trigger Monnify's generic
     * malformed syntax response, while our local records keep the rich metadata.
     *
     * @param  array<string,mixed>  $metadata
     * @return array<string,mixed>
     */
    protected function metadata(array $metadata): array
    {
        $normalized = [];

        foreach ($metadata as $key => $value) {
            $key = trim((string) $key);

            if ($key === '' || $value === null) {
                continue;
            }

            if (is_scalar($value)) {
                $normalized[$key] = $value;
                continue;
            }

            $encoded = json_encode($value, JSON_UNESCAPED_SLASHES);

            if ($encoded !== false) {
                $normalized[$key] = $encoded;
            }
        }

        return $normalized;
    }

    /**
     * Look up the current state of a transaction by paymentReference.
     */
    public function verifyTransactionByReference(string $paymentReference): array
    {
        $query = http_build_query(['paymentReference' => $paymentReference]);

        return $this->request('GET', "/api/v2/merchant/transactions/query?{$query}");
    }

    /**
     * Look up by transactionReference (the reference Monnify itself returns).
     */
    public function getTransactionStatus(string $transactionReference): array
    {
        return $this->request('GET', '/api/v2/transactions/' . urlencode($transactionReference));
    }

    /**
     * Pay-with-bank-transfer: pull dynamic virtual account details for a transaction.
     */
    public function payWithBankTransfer(string $transactionReference, ?string $bankCode = null): array
    {
        $body = ['transactionReference' => $transactionReference];

        if ($bankCode) {
            $body['bankCode'] = $bankCode;
        }

        return $this->request('POST', '/api/v1/merchant/bank-transfer/init-payment', $body);
    }

    /**
     * Validate a bank account before disbursement/name enquiry.
     */
    public function validateBankAccount(string $accountNumber, string $bankCode): array
    {
        $query = http_build_query([
            'accountNumber' => $accountNumber,
            'bankCode' => $bankCode,
        ]);

        return $this->request('GET', "/api/v1/disbursements/account/validate?{$query}");
    }

    /**
     * Initiate a Monnify single transfer/disbursement.
     *
     * @param  array{
     *   amount: float|int,
     *   reference: string,
     *   narration: string,
     *   destinationBankCode: string,
     *   destinationAccountNumber: string,
     *   destinationAccountName: string,
     *   currency?: string,
     *   sourceAccountNumber?: string,
     *   async?: bool,
     *   senderInfo?: array<string,mixed>
     * }  $payload
     */
    public function initiateSingleTransfer(array $payload): array
    {
        $body = [
            'amount' => $payload['amount'],
            'reference' => $payload['reference'],
            'narration' => $payload['narration'],
            'destinationBankCode' => $payload['destinationBankCode'],
            'destinationAccountNumber' => $payload['destinationAccountNumber'],
            'destinationAccountName' => $payload['destinationAccountName'],
            'currency' => $payload['currency'] ?? config('monnify.currency', 'NGN'),
            'sourceAccountNumber' => $payload['sourceAccountNumber'] ?? config('monnify.disbursement.source_account_number'),
        ];

        if (array_key_exists('async', $payload)) {
            $body['async'] = (bool) $payload['async'];
        }

        if (! empty($payload['senderInfo'])) {
            $body['senderInfo'] = $payload['senderInfo'];
        }

        if (empty($body['sourceAccountNumber'])) {
            throw new RuntimeException('Monnify disbursement source account number is not configured.');
        }

        return $this->request('POST', '/api/v2/disbursements/single', $body);
    }

    public function authorizeSingleTransfer(string $reference, string $authorizationCode): array
    {
        return $this->request('POST', '/api/v2/disbursements/single/validate-otp', [
            'reference' => $reference,
            'authorizationCode' => $authorizationCode,
        ]);
    }

    public function resendSingleTransferOtp(string $reference): array
    {
        return $this->request('POST', '/api/v2/disbursements/single/resend-otp', [
            'reference' => $reference,
        ]);
    }

    public function getSingleTransferStatus(string $reference): array
    {
        $query = http_build_query(['reference' => $reference]);

        return $this->request('GET', "/api/v2/disbursements/single/summary?{$query}");
    }

    public function getDisbursementWalletBalance(?string $accountNumber = null): array
    {
        $accountNumber = $accountNumber ?: config('monnify.disbursement.source_account_number');

        if (! $accountNumber) {
            throw new RuntimeException('Monnify disbursement source account number is not configured.');
        }

        $query = http_build_query(['accountNumber' => $accountNumber]);

        return $this->request('GET', "/api/v2/disbursements/wallet-balance?{$query}");
    }

    // ---------------------------------------------------------------------
    // Bills payment (airtime, data, cable TV, electricity)
    // ---------------------------------------------------------------------

    /**
     * Billers for a category. Monnify does not reliably apply the categoryCode
     * filter, so the result is filtered here as well.
     *
     * @return array<int,array<string,mixed>>
     */
    public function billers(?string $categoryCode = null): array
    {
        $billers = $this->paginate('/api/v1/vas/bills-payment/billers', array_filter(['categoryCode' => $categoryCode]));

        if (! $categoryCode) {
            return $billers;
        }

        return array_values(array_filter($billers, function ($biller) use ($categoryCode) {
            foreach ($biller['categories'] ?? [] as $category) {
                if (strcasecmp((string) ($category['code'] ?? ''), $categoryCode) === 0) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * Products for a biller, or every product when no biller is given. Monnify ignores
     * the billerCode filter (live returns its whole catalogue for any biller), so the
     * result is filtered here.
     *
     * @return array<int,array<string,mixed>>
     */
    public function billerProducts(?string $billerCode = null): array
    {
        $products = $this->paginate('/api/v1/vas/bills-payment/biller-products', array_filter(['billerCode' => $billerCode]));

        if (! $billerCode) {
            return $products;
        }

        return array_values(array_filter($products, function ($product) use ($billerCode) {
            return strcasecmp((string) data_get($product, 'biller.code'), $billerCode) === 0;
        }));
    }

    /**
     * Validate a customer ID (phone number, meter or smartcard) for a product.
     * The response carries vendInstruction.requireValidationRef and, when it is
     * true, the validationReference the vend must send back.
     */
    public function validateBillCustomer(string $productCode, string $customerId): array
    {
        return $this->billsResult(
            $this->send('POST', '/api/v1/vas/bills-payment/validate-customer', [
                'productCode' => $productCode,
                'customerId'  => $customerId,
            ]),
            'validate-customer'
        );
    }

    /**
     * Buy a bill from the merchant's Monnify wallet. vendReference must be unique
     * per purchase: it is how the result is requeried and what stops a retry
     * from buying twice.
     *
     * @param  array{
     *   productCode: string,
     *   customerId: string,
     *   vendAmount: float|int,
     *   vendReference: string,
     *   validationReference?: string|null,
     * } $payload
     */
    public function vendBill(array $payload): array
    {
        $body = [
            'productCode'   => $payload['productCode'],
            'customerId'    => $payload['customerId'],
            'vendAmount'    => $payload['vendAmount'],
            'vendReference' => $payload['vendReference'],
        ];

        if (! empty($payload['validationReference'])) {
            $body['validationReference'] = $payload['validationReference'];
        }

        return $this->billsResult($this->send('POST', '/api/v1/vas/bills-payment/vend', $body, 60), 'vend');
    }

    /**
     * The current state of a vend, or null when Monnify has no record of the reference.
     */
    public function requeryBill(string $vendReference): ?array
    {
        $query = http_build_query(['reference' => $vendReference]);
        $response = $this->send('GET', "/api/v1/vas/bills-payment/requery?{$query}");

        if ($response->status() === 404) {
            return null;
        }

        return $this->billsResult($response, 'requery');
    }

    /**
     * Collect every page of a bills listing. Monnify pages with `page` and `size`
     * (it ignores `pageNumber`/`pageSize`) and reports the next page in `nextPage`.
     *
     * @param  array<string,mixed>  $query
     * @return array<int,array<string,mixed>>
     */
    protected function paginate(string $path, array $query = [], int $maxPages = 50): array
    {
        $items = [];
        $page = 0;

        for ($fetched = 0; $fetched < $maxPages; $fetched++) {
            $body = $this->request('GET', $path . '?' . http_build_query($query + ['page' => $page, 'size' => 100]));
            $content = $body['content'] ?? [];

            array_push($items, ...$content);

            $nextPage = $body['nextPage'] ?? null;

            // Stop at the last page, once every item is collected, or if Monnify repeats a page.
            if ($nextPage === null
                || $content === []
                || (int) $nextPage <= $page
                || (isset($body['totalElements']) && count($items) >= (int) $body['totalElements'])) {
                break;
            }

            $page = (int) $nextPage;
        }

        return $items;
    }

    /**
     * @return array<string,mixed>
     */
    protected function billsResult(Response $response, string $action): array
    {
        $data = $response->json() ?? [];

        if ($response->successful() && ($data['requestSuccessful'] ?? false)) {
            return $data['responseBody'] ?? [];
        }

        Log::warning('Monnify bills request failed', [
            'action' => $action,
            'status' => $response->status(),
            'body'   => $data,
        ]);

        throw new MonnifyBillsException(
            (string) ($data['responseMessage'] ?? 'Monnify bills request failed'),
            $response->status(),
            (string) ($data['responseCode'] ?? '')
        );
    }

    // ---------------------------------------------------------------------
    // Internal HTTP helper
    // ---------------------------------------------------------------------

    /**
     * @param  array<string,mixed>  $body
     * @return array<string,mixed>
     */
    protected function request(string $method, string $path, array $body = []): array
    {
        try {
            $response = $this->send($method, $path, $body);

            $data = $response->json() ?? [];

            if (! $response->successful() || ! ($data['requestSuccessful'] ?? false)) {
                Log::warning('Monnify request failed', [
                    'path'   => $path,
                    'status' => $response->status(),
                    'body'   => $data,
                ]);

                throw new RuntimeException(
                    $data['responseMessage'] ?? 'Monnify request failed',
                    (int) ($data['responseCode'] ?? $response->status())
                );
            }

            return $data['responseBody'] ?? [];
        } catch (RequestException $e) {
            Log::error('Monnify HTTP error', ['error' => $e->getMessage()]);
            throw new RuntimeException('Monnify request error: ' . $e->getMessage());
        }
    }

    /**
     * Send an authenticated request, refreshing the access token once on a 401.
     *
     * @param  array<string,mixed>  $body
     */
    protected function send(string $method, string $path, array $body = [], ?int $timeout = null): Response
    {
        $response = $this->dispatch($this->getAccessToken(), $method, $path, $body, $timeout);

        // If the token expired between cache hit and the request, refresh once and retry.
        if ($response->status() === 401) {
            $response = $this->dispatch($this->getAccessToken(forceRefresh: true), $method, $path, $body, $timeout);
        }

        return $response;
    }

    /**
     * @param  array<string,mixed>  $body
     */
    protected function dispatch(string $token, string $method, string $path, array $body, ?int $timeout): Response
    {
        $request = Http::withToken($token)->acceptJson();

        if ($timeout) {
            $request = $request->timeout($timeout);
        }

        return match (strtoupper($method)) {
            'GET'  => $request->get($this->baseUrl . $path),
            'POST' => $request->post($this->baseUrl . $path, $body),
            default => throw new RuntimeException("Unsupported HTTP method: {$method}"),
        };
    }

    protected function ensureConfigured(): void
    {
        if ($this->apiKey === '' || $this->secretKey === '' || $this->contractCode === '') {
            throw new RuntimeException('Monnify credentials are not configured. Check your .env file.');
        }
    }
}
