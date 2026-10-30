<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\User;
use App\Services\VtuPaymentService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VtuPaymentServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'vtu.provider.driver' => 'mock',
            'monnify.base_url' => 'https://sandbox.monnify.com',
            'monnify.api_key' => 'test-api-key',
            'monnify.secret_key' => 'test-secret-key',
            'monnify.contract_code' => '1234567890',
        ]);

        Cache::flush();

        $this->app['db']->purge('sqlite');
        $this->app['db']->reconnect('sqlite');

        $this->createSchema();
    }

    public function test_initialize_payment_creates_vtu_payment_without_a_beneficiary(): void
    {
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
                    'checkoutUrl' => 'https://checkout.example/vtu',
                ],
            ]),
        ]);

        $user = User::create([
            'name' => 'VTU User',
            'email' => 'vtu-user@example.com',
            'phone' => '+2348012345000',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'user_type' => 'user',
            'blocked' => 0,
            'country' => 1,
        ]);

        $result = app(VtuPaymentService::class)->initializePayment([
            'service_type' => 'airtime',
            'provider_code' => 'mtn',
            'customer_reference' => '+2348012345000',
            'amount' => 100,
            'metadata' => [
                'source' => 'mobile-app',
                'nested' => [
                    'should_not' => 'go_to_monnify_as_an_object',
                ],
            ],
        ], $user);

        $payment = $result['payment']->fresh();
        $vtuTransaction = $result['vtu_transaction']->fresh();

        $this->assertSame('vtu', $payment->transaction_type);
        $this->assertSame(Transaction::STATUS_PENDING, $payment->status);
        $this->assertNull($payment->beneficiary_id);
        $this->assertNull($payment->beneficiary_account_id);
        $this->assertSame('MNFY|123456789', $payment->transaction_reference);
        $this->assertSame('https://checkout.example/vtu', $payment->checkout_url);
        $this->assertSame($vtuTransaction->reference, $payment->metadata['_vtu']['reference']);
        $this->assertSame(0, DB::table('beneficiaries')->count());

        $this->assertDatabaseHas('vtu_transactions', [
            'user_id' => $user->id,
            'payment_reference' => $payment->payment_reference,
            'service_type' => 'airtime',
            'customer_reference' => '+2348012345000',
            'status' => 'PENDING',
        ]);

        Http::assertSent(function (Request $request) use ($payment, $vtuTransaction) {
            if ($request->url() !== 'https://sandbox.monnify.com/api/v1/merchant/transactions/init-transaction') {
                return false;
            }

            $payload = $request->data();

            return $payload['paymentReference'] === $payment->payment_reference
                && ! array_key_exists('paymentMethods', $payload)
                && ($payload['metaData']['vtu_reference'] ?? null) === $vtuTransaction->reference
                && ($payload['metaData']['vtu_service_type'] ?? null) === 'airtime'
                && ($payload['metaData']['source'] ?? null) === 'mobile-app'
                && ! array_key_exists('_vtu', $payload['metaData'] ?? [])
                && ! array_key_exists('nested', $payload['metaData'] ?? []);
        });
    }

    protected function createSchema(): void
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->string('user_type')->default('user');
            $table->boolean('blocked')->default(false);
            $table->unsignedBigInteger('country')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('beneficiaries', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('country_code')->nullable();
            $table->string('relationship')->nullable();
            $table->string('gender')->nullable();
            $table->boolean('deleted')->default(false);
            $table->text('extra_datas')->nullable();
            $table->timestamps();
        });

        Schema::create('beneficiary_accounts', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->string('beneficiary_account_number')->nullable();
            $table->string('beneficiary_account_name')->nullable();
            $table->string('beneficiary_bank_name')->nullable();
            $table->string('beneficiary_bank_code')->nullable();
            $table->text('extra_datas')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('payment_reference')->nullable()->unique();
            $table->string('transaction_reference')->nullable()->index();
            $table->string('transaction_type')->nullable()->index();
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->unsignedBigInteger('beneficiary_account_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->double('transaction_amount')->nullable();
            $table->string('beneficiary_account_number')->nullable();
            $table->string('beneficiary_account_name')->nullable();
            $table->string('beneficiary_bank_name')->nullable();
            $table->string('beneficiary_bank_code')->nullable();
            $table->string('payment_provider')->nullable()->index();
            $table->string('transaction_status')->nullable();
            $table->string('payment_intent')->nullable();
            $table->text('payment_intent_data')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('amount_paid', 15, 2)->nullable();
            $table->decimal('fee', 15, 2)->nullable();
            $table->string('currency', 5)->default('NGN');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('payment_description')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->default('PENDING')->index();
            $table->string('checkout_url')->nullable();
            $table->json('metadata')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('vtu_transactions', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('reference')->unique();
            $table->string('payment_reference')->nullable()->index();
            $table->string('provider_reference')->nullable()->index();
            $table->string('service_type')->index();
            $table->string('provider_code')->index();
            $table->string('product_code')->nullable()->index();
            $table->string('customer_reference')->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->nullable();
            $table->string('currency', 5)->default('NGN');
            $table->string('status')->default('PENDING')->index();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }
}
