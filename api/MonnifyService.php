<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
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
        $this->baseUrl      = rtrim(config('monnify.base_url'), '/');
        $this->apiKey       = (string) config('monnify.api_key');
        $this->secretKey    = (string) config('monnify.secret_key');
        $this->contractCode = (string) config('monnify.contract_code');

        if ($this->apiKey === '' || $this->secretKey === '' || $this->contractCode === '') {
            throw new RuntimeException('Monnify credentials are not configured. Check your .env file.');
        }
    }

    /**
     * Get a Bearer access token. Tokens last ~1 hour, so we cache for 50 minutes.
     */
    public function getAccessToken(bool $forceRefresh = false): string
    {
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
                Log::error('Monnify auth failed', ['response' => $response->body()]);
                throw new RuntimeException('Could not authenticate with Monnify: ' . $response->body());
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
            'paymentMethods'     => $payload['paymentMethods'] ?? ['CARD', 'ACCOUNT_TRANSFER', 'USSD', 'PHONE_NUMBER'],
        ];

        if (! empty($payload['metadata'])) {
            $body['metaData'] = $payload['metadata'];
        }

        return $this->request('POST', '/api/v1/merchant/transactions/init-transaction', $body);
    }

    /**
     * Look up the current state of a transaction by paymentReference.
     */
    public function verifyTransactionByReference(string $paymentReference): array
    {
        return $this->request('GET', '/api/v2/transactions/' . urlencode($paymentReference));
    }

    /**
     * Look up by transactionReference (the reference Monnify itself returns).
     */
    public function getTransactionStatus(string $transactionReference): array
    {
        $query = http_build_query(['transactionReference' => $transactionReference]);

        return $this->request('GET', "/api/v2/transactions/?{$query}");
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

    // ---------------------------------------------------------------------
    // Internal HTTP helper
    // ---------------------------------------------------------------------

    /**
     * @param  array<string,mixed>  $body
     * @return array<string,mixed>
     */
    protected function request(string $method, string $path, array $body = []): array
    {
        $token = $this->getAccessToken();

        try {
            $request = Http::withToken($token)->acceptJson();

            $response = match (strtoupper($method)) {
                'GET'  => $request->get($this->baseUrl . $path),
                'POST' => $request->post($this->baseUrl . $path, $body),
                default => throw new RuntimeException("Unsupported HTTP method: {$method}"),
            };

            // If the token expired between cache hit and the request, refresh once and retry.
            if ($response->status() === 401) {
                $token = $this->getAccessToken(forceRefresh: true);
                $request = Http::withToken($token)->acceptJson();

                $response = match (strtoupper($method)) {
                    'GET'  => $request->get($this->baseUrl . $path),
                    'POST' => $request->post($this->baseUrl . $path, $body),
                };
            }

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
}
