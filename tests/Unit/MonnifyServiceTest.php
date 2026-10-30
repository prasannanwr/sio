<?php

namespace Tests\Unit;

use App\Services\MonnifyService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MonnifyServiceTest extends TestCase
{
    public function test_initialize_transaction_omits_default_payment_methods_and_flattens_metadata(): void
    {
        $this->configureMonnify([
            'monnify.redirect_url' => 'https://example.com/return',
        ]);

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'accessToken' => 'test-access-token',
                ],
            ]),
            'https://sandbox.monnify.com/api/v1/merchant/transactions/init-transaction' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'transactionReference' => 'MNFY|123456789',
                    'checkoutUrl' => 'https://checkout.example/pay',
                ],
            ]),
        ]);

        $response = app(MonnifyService::class)->initializeTransaction([
            'amount' => 350,
            'customerName' => 'SIOSHIPPING',
            'customerEmail' => 'test@example.com',
            'paymentReference' => 'VTU-PAY-123',
            'paymentDescription' => 'Data purchase for 08012345678',
            'currencyCode' => 'NGN',
            'metadata' => [
                'source' => 'mobile-app',
                '_vtu' => [
                    'reference' => 'VTU-123',
                    'service_type' => 'data',
                ],
                'empty' => null,
            ],
        ]);

        $this->assertSame('https://checkout.example/pay', $response['checkoutUrl']);

        Http::assertSent(function (Request $request) {
            if ($request->url() !== 'https://sandbox.monnify.com/api/v1/merchant/transactions/init-transaction') {
                return false;
            }

            $payload = $request->data();

            return ! array_key_exists('paymentMethods', $payload)
                && ($payload['metaData']['source'] ?? null) === 'mobile-app'
                && is_string($payload['metaData']['_vtu'] ?? null)
                && str_contains($payload['metaData']['_vtu'], '"reference":"VTU-123"')
                && ! array_key_exists('empty', $payload['metaData'] ?? []);
        });
    }

    public function test_verify_transaction_by_payment_reference_uses_query_endpoint(): void
    {
        $this->configureMonnify();

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'accessToken' => 'test-access-token',
                    'expiresIn' => 3600,
                ],
            ]),
            'https://sandbox.monnify.com/api/v2/merchant/transactions/query*' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'paymentReference' => 'VTU-PAY-123',
                    'paymentStatus' => 'PAID',
                ],
            ]),
        ]);

        $response = app(MonnifyService::class)->verifyTransactionByReference('VTU-PAY-123');

        $this->assertSame('PAID', $response['paymentStatus']);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET'
                && $request->url() === 'https://sandbox.monnify.com/api/v2/merchant/transactions/query?paymentReference=VTU-PAY-123'
                && $request->hasHeader('Authorization', 'Bearer test-access-token');
        });
    }

    public function test_initiate_single_transfer_uses_disbursement_endpoint_with_account_name(): void
    {
        $this->configureMonnify([
            'monnify.disbursement.source_account_number' => '9999999999',
        ]);

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'accessToken' => 'test-access-token',
                ],
            ]),
            'https://sandbox.monnify.com/api/v2/disbursements/single' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'reference' => 'DMP-123',
                    'status' => 'PENDING_AUTHORIZATION',
                ],
            ]),
        ]);

        $response = app(MonnifyService::class)->initiateSingleTransfer([
            'amount' => 500,
            'reference' => 'DMP-123',
            'narration' => 'Delivery man payout DMP-123',
            'destinationBankCode' => '058',
            'destinationAccountNumber' => '0123456789',
            'destinationAccountName' => 'John Doe',
            'currency' => 'NGN',
            'async' => true,
        ]);

        $this->assertSame('PENDING_AUTHORIZATION', $response['status']);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://sandbox.monnify.com/api/v2/disbursements/single'
                && $request['reference'] === 'DMP-123'
                && $request['destinationAccountName'] === 'John Doe'
                && $request['sourceAccountNumber'] === '9999999999';
        });
    }

    public function test_authentication_trims_configured_credentials(): void
    {
        $this->configureMonnify([
            'monnify.base_url' => ' https://sandbox.monnify.com/ ',
            'monnify.api_key' => ' test-api-key ',
            'monnify.secret_key' => ' test-secret-key ',
            'monnify.contract_code' => ' 1234567890 ',
        ]);

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'accessToken' => 'test-access-token',
                ],
            ]),
            'https://sandbox.monnify.com/api/v2/merchant/transactions/query*' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'paymentReference' => 'VTU-PAY-123',
                    'paymentStatus' => 'PAID',
                ],
            ]),
        ]);

        app(MonnifyService::class)->verifyTransactionByReference('VTU-PAY-123');

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://sandbox.monnify.com/api/v1/auth/login'
                && $request->hasHeader('Authorization', 'Basic ' . base64_encode('test-api-key:test-secret-key'));
        });
    }

    public function test_authentication_failure_reports_configuration_hint(): void
    {
        $this->configureMonnify();

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => Http::response([
                'requestSuccessful' => false,
                'responseMessage' => '',
                'responseCode' => '99',
            ]),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Check MONNIFY_BASE_URL, MONNIFY_API_KEY, and MONNIFY_SECRET_KEY');

        app(MonnifyService::class)->verifyTransactionByReference('VTU-PAY-123');
    }

    public function test_bills_listing_stops_when_monnify_repeats_a_page(): void
    {
        $this->configureMonnify();

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => ['accessToken' => 'test-access-token'],
            ]),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/billers*' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => [
                    'content' => [['code' => 'MTN', 'categories' => [['code' => 'AIRTIME']]]],
                    'nextPage' => 0,
                ],
            ]),
        ]);

        $billers = app(MonnifyService::class)->billers('AIRTIME');

        $this->assertCount(1, $billers);

        $listingRequests = collect(Http::recorded())->filter(function ($pair) {
            return str_contains($pair[0]->url(), '/bills-payment/billers');
        });

        $this->assertCount(1, $listingRequests);
    }

    public function test_requery_bill_returns_null_when_monnify_has_no_record(): void
    {
        $this->configureMonnify();

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => Http::response([
                'requestSuccessful' => true,
                'responseBody' => ['accessToken' => 'test-access-token'],
            ]),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/requery*' => Http::response([
                'requestSuccessful' => false,
                'responseMessage' => 'Transaction not found for reference: VTU-REF-1',
                'responseCode' => '99',
            ], 404),
        ]);

        $this->assertNull(app(MonnifyService::class)->requeryBill('VTU-REF-1'));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET'
                && $request->url() === 'https://sandbox.monnify.com/api/v1/vas/bills-payment/requery?reference=VTU-REF-1';
        });
    }

    protected function configureMonnify(array $overrides = []): void
    {
        config(array_merge([
            'monnify.base_url' => 'https://sandbox.monnify.com',
            'monnify.api_key' => 'test-api-key',
            'monnify.secret_key' => 'test-secret-key',
            'monnify.contract_code' => '1234567890',
        ], $overrides));

        Cache::flush();
    }
}
