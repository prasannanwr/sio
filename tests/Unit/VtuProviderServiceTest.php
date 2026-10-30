<?php

namespace Tests\Unit;

use App\Exceptions\MonnifyBillsException;
use App\Exceptions\VtuCustomerValidationException;
use App\Exceptions\VtuServiceUnavailableException;
use App\Models\User;
use App\Models\VtuTransaction;
use App\Services\VtuProviderService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VtuProviderServiceTest extends TestCase
{
    public function test_mock_driver_returns_configured_providers_without_http_request(): void
    {
        config([
            'vtu.provider.driver' => 'mock',
            'vtu.providers.airtime' => [
                ['code' => 'mtn', 'name' => 'MTN'],
            ],
        ]);

        Http::fake();

        $providers = app(VtuProviderService::class)->providers('airtime');

        $this->assertSame([
            ['code' => 'mtn', 'name' => 'MTN'],
        ], $providers);

        Http::assertNothingSent();
    }

    public function test_http_driver_replaces_path_parameters_and_sets_provider_headers(): void
    {
        config([
            'vtu.provider.driver' => 'http',
            'vtu.provider.base_url' => 'https://aggregator.example',
            'vtu.provider.api_key' => 'test-api-key',
            'vtu.provider.secret_key' => 'test-secret-key',
            'vtu.provider.auth_scheme' => 'Bearer',
            'vtu.provider.api_key_header' => 'Authorization',
            'vtu.provider.endpoints.providers.method' => 'GET',
            'vtu.provider.endpoints.providers.path' => '/vtu/{service_type}/providers',
        ]);

        Http::fake([
            'https://aggregator.example/vtu/airtime/providers' => Http::response([
                ['code' => 'mtn', 'name' => 'MTN'],
            ]),
        ]);

        $providers = app(VtuProviderService::class)->providers('airtime');

        $this->assertSame([
            ['code' => 'mtn', 'name' => 'MTN'],
        ], $providers);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET'
                && $request->url() === 'https://aggregator.example/vtu/airtime/providers'
                && $request->hasHeader('Authorization', 'Bearer test-api-key')
                && $request->hasHeader('X-Secret-Key', 'test-secret-key');
        });
    }

    public function test_monnify_driver_lists_airtime_networks_from_config_without_calling_monnify(): void
    {
        $this->configureMonnifyDriver();

        Http::fake();

        $this->assertSame([
            ['code' => 'mtn', 'name' => 'MTN'],
            ['code' => 'glo', 'name' => 'Glo'],
        ], app(VtuProviderService::class)->providers('airtime'));

        Http::assertNothingSent();
    }

    public function test_monnify_product_catalogue_is_fetched_once_for_all_billers(): void
    {
        $this->configureMonnifyDriver(['vtu.monnify.services' => ['airtime', 'data']]);

        Http::fake($this->monnifyCatalogFakes());

        $service = app(VtuProviderService::class);
        $service->products('airtime', 'mtn');
        $service->products('data', 'mtn');
        $service->products('airtime', 'glo');

        $catalogueRequests = collect(Http::recorded())->filter(function ($pair) {
            return str_contains($pair[0]->url(), '/bills-payment/biller-products');
        });

        $this->assertCount(1, $catalogueRequests);
    }

    public function test_monnify_purchase_validates_then_vends_the_top_up_product_in_local_phone_format(): void
    {
        $this->configureMonnifyDriver();

        Http::fake($this->monnifyCatalogFakes() + [
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer' => $this->monnifyOk([
                'customerName' => 'Test Customer',
                'vendInstruction' => ['requireValidationRef' => true],
                'validationReference' => 'VAL-123',
            ]),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend' => $this->monnifyOk([
                'vendReference' => 'VTU-REF-1',
                'transactionReference' => 'MNFY-BILL-1',
                'vendStatus' => 'SUCCESS',
            ]),
        ]);

        $service = app(VtuProviderService::class);
        $response = $service->purchase($this->purchasePayload(['customer_reference' => '+234 803 123 4567']));

        $this->assertSame(VtuTransaction::STATUS_SUCCESSFUL, $service->normalizeStatus($response));
        $this->assertSame('MNFY-BILL-1', $service->providerReference($response));

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend'
                && $request['productCode'] === '13'
                && $request['customerId'] === '08031234567'
                && (float) $request['vendAmount'] === 350.0
                && $request['vendReference'] === 'VTU-REF-1'
                && $request['validationReference'] === 'VAL-123';
        });
    }

    public function test_monnify_purchase_stays_processing_when_the_vend_times_out(): void
    {
        $this->configureMonnifyDriver();

        Http::fake($this->monnifyCatalogFakes() + [
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer' => $this->monnifyOk([
                'vendInstruction' => ['requireValidationRef' => false],
            ]),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend' => fn () => throw new ConnectionException('cURL error 28: Operation timed out'),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/requery*' => $this->monnifyOk([
                'vendReference' => 'VTU-REF-1',
                'transactionReference' => 'MNFY-BILL-1',
                'vendStatus' => 'IN_PROGRESS',
            ]),
        ]);

        $service = app(VtuProviderService::class);
        $response = $service->purchase($this->purchasePayload());

        $this->assertSame(VtuTransaction::STATUS_PROCESSING, $service->normalizeStatus($response));
        $this->assertSame('MNFY-BILL-1', $service->providerReference($response));

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://sandbox.monnify.com/api/v1/vas/bills-payment/requery?reference=VTU-REF-1';
        });
    }

    public function test_monnify_purchase_is_not_failed_when_a_server_error_leaves_the_result_unknown(): void
    {
        $this->configureMonnifyDriver();

        Http::fake($this->monnifyCatalogFakes() + [
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer' => $this->monnifyOk([
                'vendInstruction' => ['requireValidationRef' => false],
            ]),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend' => Http::response([
                'requestSuccessful' => false,
                'responseMessage' => 'Internal server error',
            ], 500),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/requery*' => Http::response([
                'requestSuccessful' => false,
                'responseMessage' => 'Transaction not found for reference: VTU-REF-1',
                'responseCode' => '99',
            ], 404),
        ]);

        $service = app(VtuProviderService::class);
        $response = $service->purchase($this->purchasePayload());

        $this->assertSame('processing', $response['status']);
        $this->assertSame(VtuTransaction::STATUS_PROCESSING, $service->normalizeStatus($response));
    }

    public function test_monnify_purchase_fails_when_monnify_rejects_the_vend(): void
    {
        $this->configureMonnifyDriver();

        Http::fake($this->monnifyCatalogFakes() + [
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer' => $this->monnifyOk([
                'vendInstruction' => ['requireValidationRef' => false],
            ]),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend' => Http::response([
                'requestSuccessful' => false,
                'responseMessage' => 'Insufficient wallet balance',
                'responseCode' => '99',
            ], 400),
        ]);

        $this->expectException(MonnifyBillsException::class);
        $this->expectExceptionMessage('Insufficient wallet balance');

        app(VtuProviderService::class)->purchase($this->purchasePayload());
    }

    public function test_monnify_preflight_rejects_an_invalid_number_before_payment(): void
    {
        $this->configureMonnifyDriver();

        Http::fake($this->monnifyCatalogFakes() + [
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer' => Http::response([
                'requestSuccessful' => false,
                'responseMessage' => 'invalid customerId',
                'responseCode' => '99',
            ], 400),
        ]);

        try {
            app(VtuProviderService::class)->preflight($this->purchasePayload());
            $this->fail('An invalid number should be rejected before payment.');
        } catch (VtuCustomerValidationException $e) {
            $this->assertStringContainsString('invalid customerId', $e->getMessage());
        }

        Http::assertNotSent(function (Request $request) {
            return str_contains($request->url(), '/bills-payment/vend');
        });
    }

    public function test_monnify_service_not_switched_on_is_unavailable_except_to_test_users(): void
    {
        $this->configureMonnifyDriver(['vtu.monnify.test_users' => [7]]);

        Http::fake();

        $service = app(VtuProviderService::class);
        $customer = new User();
        $customer->id = 3;
        $tester = new User();
        $tester->id = 7;

        $this->assertTrue($service->isServiceAvailable('airtime', $customer));
        $this->assertFalse($service->isServiceAvailable('data', $customer));
        $this->assertFalse($service->isServiceAvailable('data'));
        $this->assertTrue($service->isServiceAvailable('data', $tester));

        try {
            $service->assertServiceAvailable('data', $customer);
            $this->fail('Data should not be available to customers yet.');
        } catch (VtuServiceUnavailableException $e) {
            $this->assertSame('Data purchases are not available yet.', $e->getMessage());
        }

        $available = collect($service->services($customer))->pluck('available', 'code');

        $this->assertTrue($available['airtime']);
        $this->assertFalse($available['data']);

        Http::assertNothingSent();
    }

    public function test_monnify_purchase_already_paid_for_completes_after_the_service_is_switched_off(): void
    {
        $this->configureMonnifyDriver(['vtu.monnify.services' => ['airtime']]);

        Http::fake($this->monnifyCatalogFakes() + [
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer' => $this->monnifyOk([
                'vendInstruction' => ['requireValidationRef' => false],
            ]),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend' => $this->monnifyOk([
                'vendReference' => 'VTU-REF-1',
                'transactionReference' => 'MNFY-BILL-4',
                'vendStatus' => 'SUCCESS',
            ]),
        ]);

        $service = app(VtuProviderService::class);
        $response = $service->purchase($this->purchasePayload([
            'service_type' => 'data',
            'product_code' => '19523',
            'amount' => 500,
        ]));

        $this->assertSame(VtuTransaction::STATUS_SUCCESSFUL, $service->normalizeStatus($response));
    }

    public function test_monnify_status_reports_a_reference_monnify_does_not_know(): void
    {
        $this->configureMonnifyDriver();

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => $this->monnifyToken(),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/requery*' => Http::response([
                'requestSuccessful' => false,
                'responseMessage' => 'Transaction not found for reference: VTU-REF-1',
                'responseCode' => '99',
            ], 404),
        ]);

        $service = app(VtuProviderService::class);
        $response = $service->status('VTU-REF-1');

        $this->assertSame('not_found', $response['status']);
        $this->assertSame(VtuTransaction::STATUS_PROCESSING, $service->normalizeStatus($response));
    }

    public function test_monnify_data_products_use_the_app_product_format(): void
    {
        $this->configureMonnifyDriver(['vtu.monnify.services' => ['airtime', 'data']]);

        Http::fake($this->monnifyCatalogFakes());

        $products = app(VtuProviderService::class)->products('data', 'mtn');

        // Loose comparison: Monnify's prices can decode as 500 or 500.0.
        $this->assertEquals([
            [
                'code' => '19523',
                'name' => 'DataPlan 750MB',
                'amount' => 500.0,
                'is_variable_amount' => false,
                'price' => 500.0,
                'price_type' => 'FIXED',
                'min_amount' => null,
                'max_amount' => null,
            ],
        ], $products);
    }

    public function test_monnify_data_purchase_vends_the_chosen_bundle(): void
    {
        $this->configureMonnifyDriver(['vtu.monnify.services' => ['airtime', 'data']]);

        Http::fake($this->monnifyCatalogFakes() + [
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer' => $this->monnifyOk([
                'vendInstruction' => ['requireValidationRef' => false],
            ]),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend' => $this->monnifyOk([
                'vendReference' => 'VTU-REF-1',
                'transactionReference' => 'MNFY-BILL-2',
                'vendStatus' => 'SUCCESS',
            ]),
        ]);

        $service = app(VtuProviderService::class);
        $response = $service->purchase($this->purchasePayload([
            'service_type' => 'data',
            'product_code' => '19523',
            'amount' => 500,
            'customer_reference' => '2348031234567',
        ]));

        $this->assertSame(VtuTransaction::STATUS_SUCCESSFUL, $service->normalizeStatus($response));

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend'
                && $request['productCode'] === '19523'
                && $request['customerId'] === '08031234567'
                && (float) $request['vendAmount'] === 500.0
                && ! isset($request['validationReference']);
        });
    }

    public function test_monnify_preflight_rejects_a_data_amount_that_does_not_match_the_bundle_price(): void
    {
        $this->configureMonnifyDriver(['vtu.monnify.services' => ['airtime', 'data']]);

        Http::fake($this->monnifyCatalogFakes());

        $this->expectException(VtuCustomerValidationException::class);
        $this->expectExceptionMessage('DataPlan 750MB costs 500');

        app(VtuProviderService::class)->preflight($this->purchasePayload([
            'service_type' => 'data',
            'product_code' => '19523',
            'amount' => 350,
        ]));
    }

    public function test_monnify_cable_tv_lists_the_configured_billers_with_app_codes(): void
    {
        $this->configureMonnifyDriver($this->cableTvConfig());

        Http::fake();

        $this->assertSame([
            ['code' => 'dstv', 'name' => 'DStv'],
            ['code' => 'gotv', 'name' => 'GOtv'],
            ['code' => 'startimes', 'name' => 'StarTimes'],
        ], app(VtuProviderService::class)->providers('cable_tv'));

        Http::assertNothingSent();
    }

    public function test_monnify_cable_tv_validation_returns_the_account_name(): void
    {
        $this->configureMonnifyDriver($this->cableTvConfig());

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => $this->monnifyToken(),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/billers*' => $this->monnifyPage([
                ['code' => 'biller-gotv', 'name' => 'GOTV', 'categories' => [['code' => 'CABLE_TV']]],
            ], null),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/biller-products*' => $this->monnifyPage([
                ['code' => 'product-gotv-max', 'name' => 'GOtv Max', 'priceType' => 'FIXED', 'price' => 8600.0, 'biller' => ['code' => 'biller-gotv'], 'category' => ['code' => 'CABLE_TV']],
            ], null),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer' => $this->monnifyOk([
                'customerName' => 'ADA OBI',
                'vendInstruction' => ['requireValidationRef' => true],
                'validationReference' => 'VAL-7',
            ]),
        ]);

        $response = app(VtuProviderService::class)->validateCustomer([
            'service_type' => 'cable_tv',
            'provider_code' => 'gotv',
            'product_code' => 'product-gotv-max',
            'customer_reference' => '7012345678',
        ]);

        $this->assertSame('ADA OBI', $response['data']['customer_name']);
        $this->assertSame('7012345678', $response['data']['customer_reference']);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer'
                && $request['productCode'] === 'product-gotv-max'
                && $request['customerId'] === '7012345678';
        });
    }

    public function test_monnify_electricity_lists_prepaid_and_postpaid_and_returns_the_token(): void
    {
        $this->configureMonnifyDriver([
            'vtu.monnify.services' => ['electricity'],
            'vtu.monnify.categories' => ['electricity' => 'ELECTRICITY'],
            'vtu.monnify.billers' => ['electricity' => [
                'ikeja-electric-prepaid' => ['code' => 'biller-ikedc-pre', 'name' => 'Ikeja Electric (Prepaid)'],
                'ikeja-electric-postpaid' => ['code' => 'biller-ikedc-post', 'name' => 'Ikeja Electric (Postpaid)'],
            ]],
        ]);

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => $this->monnifyToken(),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/billers*' => $this->monnifyPage([
                ['code' => 'biller-ikedc-pre', 'name' => 'Ikeja Electricity Distribution Prepaid', 'categories' => [['code' => 'ELECTRICITY']]],
                ['code' => 'biller-ikedc-post', 'name' => 'Ikeja Electricity Distribution Postpaid', 'categories' => [['code' => 'ELECTRICITY']]],
            ], null),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/biller-products*' => $this->monnifyPage([
                ['code' => 'product-ikedc-pre', 'name' => 'Ikeja Electricity Distribution Prepaid', 'priceType' => 'OPEN', 'price' => null, 'minAmount' => 500.0, 'maxAmount' => 100000.0, 'biller' => ['code' => 'biller-ikedc-pre'], 'category' => ['code' => 'ELECTRICITY']],
                ['code' => 'product-ikedc-post', 'name' => 'Ikeja Electricity Distribution Postpaid', 'priceType' => 'OPEN', 'price' => null, 'minAmount' => 1.0, 'maxAmount' => 100000.0, 'biller' => ['code' => 'biller-ikedc-post'], 'category' => ['code' => 'ELECTRICITY']],
            ], null),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/validate-customer' => $this->monnifyOk([
                'customerName' => 'ADA OBI',
                'vendInstruction' => ['requireValidationRef' => true],
                'validationReference' => 'VAL-9',
            ]),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend' => $this->monnifyOk([
                'vendReference' => 'VTU-REF-1',
                'transactionReference' => 'MNFY-BILL-3',
                'vendStatus' => 'SUCCESS',
                'token' => '1234-5678-9012-3456-7890',
                'units' => '42.5',
            ]),
        ]);

        $service = app(VtuProviderService::class);

        $this->assertSame([
            ['code' => 'ikeja-electric-prepaid', 'name' => 'Ikeja Electric (Prepaid)'],
            ['code' => 'ikeja-electric-postpaid', 'name' => 'Ikeja Electric (Postpaid)'],
        ], $service->providers('electricity'));

        $response = $service->purchase($this->purchasePayload([
            'service_type' => 'electricity',
            'provider_code' => 'ikeja-electric-prepaid',
            'customer_reference' => ' 45012345678 ',
            'amount' => 5000,
        ]));

        $this->assertSame(VtuTransaction::STATUS_SUCCESSFUL, $service->normalizeStatus($response));

        $transaction = new VtuTransaction(['provider_response' => $response]);

        $this->assertSame([
            'token' => '1234-5678-9012-3456-7890',
            'units' => '42.5',
        ], $transaction->purchaseDetails());

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://sandbox.monnify.com/api/v1/vas/bills-payment/vend'
                && $request['productCode'] === 'product-ikedc-pre'
                && $request['customerId'] === '45012345678'
                && $request['validationReference'] === 'VAL-9';
        });
    }

    public function test_monnify_electricity_rejects_an_amount_below_the_company_minimum(): void
    {
        $this->configureMonnifyDriver([
            'vtu.monnify.services' => ['electricity'],
            'vtu.monnify.categories' => ['electricity' => 'ELECTRICITY'],
            'vtu.monnify.billers' => ['electricity' => [
                'ikeja-electric-prepaid' => ['code' => 'biller-ikedc-pre', 'name' => 'Ikeja Electric (Prepaid)'],
            ]],
        ]);

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => $this->monnifyToken(),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/billers*' => $this->monnifyPage([
                ['code' => 'biller-ikedc-pre', 'name' => 'Ikeja Electricity Distribution Prepaid', 'categories' => [['code' => 'ELECTRICITY']]],
            ], null),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/biller-products*' => $this->monnifyPage([
                ['code' => 'product-ikedc-pre', 'name' => 'Ikeja Electricity Distribution Prepaid', 'priceType' => 'OPEN', 'price' => null, 'minAmount' => 500.0, 'biller' => ['code' => 'biller-ikedc-pre'], 'category' => ['code' => 'ELECTRICITY']],
            ], null),
        ]);

        $this->expectException(VtuCustomerValidationException::class);
        $this->expectExceptionMessage('The minimum amount for Ikeja Electricity Distribution Prepaid is 500');

        app(VtuProviderService::class)->preflight($this->purchasePayload([
            'service_type' => 'electricity',
            'provider_code' => 'ikeja-electric-prepaid',
            'customer_reference' => '45012345678',
            'amount' => 200,
        ]));
    }

    public function test_monnify_electricity_company_without_a_product_is_reported_unavailable(): void
    {
        $this->configureMonnifyDriver([
            'vtu.monnify.services' => ['electricity'],
            'vtu.monnify.categories' => ['electricity' => 'ELECTRICITY'],
            'vtu.monnify.billers' => ['electricity' => [
                'kano-electric-prepaid' => ['code' => 'biller-kedc-pre', 'name' => 'Kano Electricity (Prepaid)'],
            ]],
        ]);

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => $this->monnifyToken(),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/billers*' => $this->monnifyPage([
                ['code' => 'biller-kedc-pre', 'name' => 'Kano Electricity Distribution Prepaid', 'categories' => [['code' => 'ELECTRICITY']]],
            ], null),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/biller-products*' => $this->monnifyPage([
                ['code' => 'product-ikedc-pre', 'name' => 'Ikeja Electricity Distribution Prepaid', 'priceType' => 'OPEN', 'price' => null, 'biller' => ['code' => 'biller-ikedc-pre'], 'category' => ['code' => 'ELECTRICITY']],
            ], null),
        ]);

        $this->expectException(VtuServiceUnavailableException::class);
        $this->expectExceptionMessage('Kano Electricity (Prepaid) is not available right now.');

        app(VtuProviderService::class)->preflight($this->purchasePayload([
            'service_type' => 'electricity',
            'provider_code' => 'kano-electric-prepaid',
            'customer_reference' => '45012345678',
            'amount' => 1000,
        ]));
    }

    public function test_monnify_bills_reject_a_biller_that_is_not_offered(): void
    {
        $this->configureMonnifyDriver([
            'vtu.monnify.services' => ['bills'],
            'vtu.monnify.categories' => ['bills' => 'EDUCATION'],
            'vtu.monnify.billers' => ['bills' => [
                'waec' => ['code' => 'biller-ed-waec', 'name' => 'WAEC'],
            ]],
        ]);

        Http::fake([
            'https://sandbox.monnify.com/api/v1/auth/login' => $this->monnifyToken(),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/billers*' => $this->monnifyPage([
                ['code' => 'biller-ed-waec', 'name' => 'Nigeria WAEC', 'categories' => [['code' => 'EDUCATION']]],
            ], null),
        ]);

        $service = app(VtuProviderService::class);

        $this->assertSame([['code' => 'waec', 'name' => 'WAEC']], $service->providers('bills'));

        $this->expectException(VtuCustomerValidationException::class);
        $this->expectExceptionMessage('The provider [government] is not available.');

        $service->products('bills', 'government');
    }

    protected function cableTvConfig(): array
    {
        return [
            'vtu.monnify.services' => ['cable_tv'],
            'vtu.monnify.categories' => ['cable_tv' => 'CABLE_TV'],
            'vtu.monnify.billers' => ['cable_tv' => [
                'dstv' => ['code' => 'biller-dstv', 'name' => 'DStv'],
                'gotv' => ['code' => 'biller-gotv', 'name' => 'GOtv'],
                'startimes' => ['code' => 'biller-startimes', 'name' => 'StarTimes'],
            ]],
        ];
    }

    protected function configureMonnifyDriver(array $overrides = []): void
    {
        config(array_merge([
            'vtu.provider.driver' => 'monnify',
            'vtu.monnify.services' => ['airtime'],
            'vtu.monnify.categories' => ['airtime' => 'AIRTIME', 'data' => 'DATA_BUNDLE'],
            'vtu.monnify.products' => [],
            'vtu.monnify.test_users' => [],
            'vtu.monnify.billers' => [
                'airtime' => [
                    'mtn' => ['code' => 'MTN', 'name' => 'MTN'],
                    'glo' => ['code' => 'GLO', 'name' => 'Glo'],
                ],
                'data' => [
                    'mtn' => ['code' => 'MTN', 'name' => 'MTN'],
                ],
            ],
            'vtu.monnify.validate_before_payment' => true,
            'vtu.services.data.label' => 'Data',
            'vtu.providers.airtime' => [
                ['code' => 'mtn', 'name' => 'MTN'],
                ['code' => 'glo', 'name' => 'Glo'],
            ],
            'monnify.base_url' => 'https://sandbox.monnify.com',
            'monnify.api_key' => 'test-api-key',
            'monnify.secret_key' => 'test-secret-key',
            'monnify.contract_code' => '1234567890',
        ], $overrides));

        Cache::flush();
    }

    /**
     * Billers and products shaped like Monnify's sandbox catalogue, where the
     * billerCode filter is ignored and other networks' products come back too.
     */
    protected function monnifyCatalogFakes(): array
    {
        return [
            'https://sandbox.monnify.com/api/v1/auth/login' => $this->monnifyToken(),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/billers*' => $this->monnifyPage([
                ['code' => 'MTN', 'name' => 'MTN', 'categories' => [['code' => 'AIRTIME'], ['code' => 'DATA_BUNDLE']]],
            ], null),
            'https://sandbox.monnify.com/api/v1/vas/bills-payment/biller-products*' => $this->monnifyPage([
                ['code' => '245', 'name' => 'Xtratalk 300', 'priceType' => 'OPEN', 'price' => null, 'biller' => ['code' => 'MTN'], 'category' => ['code' => 'AIRTIME']],
                ['code' => '13', 'name' => 'MTN Mobile Top up', 'priceType' => 'OPEN', 'price' => null, 'biller' => ['code' => 'MTN'], 'category' => ['code' => 'AIRTIME']],
                ['code' => '19523', 'name' => 'DataPlan 750MB', 'priceType' => 'FIXED', 'price' => 500.0, 'biller' => ['code' => 'MTN'], 'category' => ['code' => 'DATA_BUNDLE']],
                ['code' => '11', 'name' => 'Airtel Mobile Top up', 'priceType' => 'OPEN', 'price' => null, 'biller' => ['code' => 'AIRTEL'], 'category' => ['code' => 'AIRTIME']],
            ], null),
        ];
    }

    protected function purchasePayload(array $overrides = []): array
    {
        return array_merge([
            'reference' => 'VTU-REF-1',
            'service_type' => 'airtime',
            'provider_code' => 'mtn',
            'product_code' => null,
            'customer_reference' => '08031234567',
            'amount' => 350,
        ], $overrides);
    }

    protected function monnifyToken()
    {
        return $this->monnifyOk(['accessToken' => 'test-access-token']);
    }

    protected function monnifyOk(array $body)
    {
        return Http::response([
            'requestSuccessful' => true,
            'responseMessage' => 'success',
            'responseCode' => '0',
            'responseBody' => $body,
        ]);
    }

    protected function monnifyPage(array $content, ?int $nextPage)
    {
        return $this->monnifyOk([
            'content' => $content,
            'nextPage' => $nextPage,
        ]);
    }
}
