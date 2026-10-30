<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_signup_verify_login_and_use_jwt(): void
    {
        $signup = $this->postJson('/api/auth/signup', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '+234 801 234 5678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $otp = $signup->json('data.debug_otp');
        $this->assertNotEmpty($otp);

        $verify = $this->postJson('/api/auth/verify-otp', [
            'identifier' => 'ada@example.com',
            'otp' => $otp,
        ])->assertOk();

        $token = $verify->json('data.token.access_token');
        $this->assertNotEmpty($token);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('data.user.email', 'ada@example.com')
            ->assertJsonPath('data.user.is_verified', true);

        $this->postJson('/api/auth/login', [
            'identifier' => '+2348012345678',
            'password' => 'password123',
        ])->assertOk()
            ->assertJsonPath('data.user.email', 'ada@example.com');
    }

    public function test_logout_revokes_current_jwt(): void
    {
        $signup = $this->postJson('/api/auth/signup', [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $token = $this->postJson('/api/auth/verify-otp', [
            'identifier' => 'grace@example.com',
            'otp' => $signup->json('data.debug_otp'),
        ])->assertOk()->json('data.token.access_token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout')
            ->assertOk();

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me')
            ->assertUnauthorized();
    }

    public function test_forgot_password_resets_password_with_otp(): void
    {
        $signup = $this->postJson('/api/auth/signup', [
            'name' => 'Katherine Johnson',
            'email' => 'katherine@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $this->postJson('/api/auth/verify-otp', [
            'identifier' => 'katherine@example.com',
            'otp' => $signup->json('data.debug_otp'),
        ])->assertOk();

        $forgot = $this->postJson('/api/auth/forgot-password', [
            'identifier' => 'katherine@example.com',
        ])->assertOk();

        $this->postJson('/api/auth/reset-password', [
            'identifier' => 'katherine@example.com',
            'otp' => $forgot->json('data.debug_otp'),
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertOk();

        $this->postJson('/api/auth/login', [
            'identifier' => 'katherine@example.com',
            'password' => 'newpassword123',
        ])->assertOk();
    }
}
