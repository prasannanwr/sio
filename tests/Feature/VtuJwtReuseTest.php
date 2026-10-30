<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VtuJwtReuseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'vtu.provider.driver' => 'mock',
        ]);

        $this->app['db']->purge('sqlite');
        $this->app['db']->reconnect('sqlite');

        $this->createSchema();
    }

    public function test_vtu_routes_accept_the_same_custom_jwt_for_repeated_requests(): void
    {
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

        $token = app(JwtService::class)->issueToken($user)['access_token'];
        $headers = ['Authorization' => 'Bearer ' . $token];
        $tokenHash = hash('sha256', $token);
        $issuedToken = DB::table('jwt_tokens')->where('token_hash', $tokenHash)->first();

        $this->withHeaders($headers)
            ->getJson('/api/vtu/services')
            ->assertOk()
            ->assertJsonPath('status', true);

        $this->withHeaders($headers)
            ->getJson('/api/vtu/dashboard')
            ->assertOk()
            ->assertJsonPath('status', true);

        $this->withHeaders($headers)
            ->getJson('/api/vtu/dashboard')
            ->assertOk()
            ->assertJsonPath('status', true);

        $currentToken = DB::table('jwt_tokens')->where('token_hash', $tokenHash)->first();

        $this->assertNull($currentToken->revoked_at);
        $this->assertNull($currentToken->last_used_at);
        $this->assertSame((string) $issuedToken->expires_at, (string) $currentToken->expires_at);
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

        Schema::create('jwt_tokens', function ($table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('jti')->unique();
            $table->string('token_hash', 64)->unique();
            $table->dateTime('expires_at')->index();
            $table->dateTime('last_used_at')->nullable();
            $table->dateTime('revoked_at')->nullable()->index();
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
