<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VtuMonnifyTestUsersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'vtu.provider.driver' => 'monnify',
            'vtu.monnify.services' => ['airtime'],
            'vtu.monnify.test_users' => [],
        ]);

        Cache::flush();

        $this->app['db']->purge('sqlite');
        $this->app['db']->reconnect('sqlite');

        $this->createSchema();
    }

    public function test_customers_get_503_for_a_service_that_is_not_switched_on(): void
    {
        Http::fake();

        $customer = $this->createUser('customer@example.com', '+2348012345001');
        $tester = $this->createUser('tester@example.com', '+2348012345002');

        config(['vtu.monnify.test_users' => [$tester->id]]);

        $headers = $this->authHeaders($customer);

        $this->withHeaders($headers)
            ->getJson('/api/vtu/providers?service_type=cable_tv')
            ->assertStatus(503)
            ->assertJsonPath('message', 'Cable TV purchases are not available yet.');

        $this->withHeaders($headers)
            ->getJson('/api/vtu/services')
            ->assertOk()
            ->assertJsonPath('data.0.code', 'airtime')
            ->assertJsonPath('data.0.available', true)
            ->assertJsonPath('data.2.code', 'cable_tv')
            ->assertJsonPath('data.2.available', false);

        Http::assertNothingSent();
    }

    public function test_test_users_can_use_a_service_that_is_not_switched_on(): void
    {
        Http::fake();

        $tester = $this->createUser('tester@example.com', '+2348012345002');

        config(['vtu.monnify.test_users' => [$tester->id]]);

        $headers = $this->authHeaders($tester);

        $this->withHeaders($headers)
            ->getJson('/api/vtu/providers?service_type=cable_tv')
            ->assertOk()
            ->assertJsonPath('data.0.code', 'dstv');

        $this->withHeaders($headers)
            ->getJson('/api/vtu/services')
            ->assertOk()
            ->assertJsonPath('data.2.available', true);

        Http::assertNothingSent();
    }

    protected function createUser(string $email, string $phone): User
    {
        return User::create([
            'name' => 'VTU User',
            'email' => $email,
            'phone' => $phone,
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'user_type' => 'user',
            'blocked' => 0,
            'country' => 1,
        ]);
    }

    protected function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer ' . app(JwtService::class)->issueToken($user)['access_token']];
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
    }
}
