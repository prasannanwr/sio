<?php

namespace App\Services;

use App\Models\JwtToken;
use App\Models\User;
use Illuminate\Support\Str;
use RuntimeException;

class JwtService
{
    public function issueToken(User $user): array
    {
        $issuedAt = now();
        $ttlMinutes = (int) config('auth_api.jwt.ttl_minutes', 10080);
        $expiresAt = $issuedAt->copy()->addMinutes($ttlMinutes);
        $jti = (string) Str::uuid();

        $payload = [
            'iss' => config('auth_api.jwt.issuer'),
            'sub' => (string) $user->id,
            'jti' => $jti,
            'iat' => $issuedAt->timestamp,
            'nbf' => $issuedAt->timestamp,
            'exp' => $expiresAt->timestamp,
        ];

        $token = $this->encode($payload);

        JwtToken::create([
            'user_id' => $user->id,
            'jti' => $jti,
            'token_hash' => hash('sha256', $token),
            'expires_at' => $expiresAt,
        ]);

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttlMinutes * 60,
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    public function authenticate(string $token): array
    {
        $payload = $this->decode($token);
        $now = now()->timestamp;

        if (($payload['nbf'] ?? 0) > $now || ($payload['exp'] ?? 0) <= $now) {
            throw new RuntimeException('Token has expired.');
        }

        $tokenRecord = JwtToken::where('jti', $payload['jti'] ?? null)
            ->where('token_hash', hash('sha256', $token))
            ->first();

        if (! $tokenRecord || ! $tokenRecord->isActive()) {
            throw new RuntimeException('Token is not active.');
        }

        $user = User::find($payload['sub'] ?? null);

        if (! $user) {
            throw new RuntimeException('Token user was not found.');
        }

        return [$user, $tokenRecord, $payload];
    }

    public function revokeToken(JwtToken $token): void
    {
        $token->forceFill(['revoked_at' => now()])->save();
    }

    public function revokeAllForUser(User $user): void
    {
        JwtToken::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    protected function encode(array $payload): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload)),
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $this->secret(), true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    protected function decode(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new RuntimeException('Token format is invalid.');
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $header = json_decode($this->base64UrlDecode($encodedHeader), true);
        $payload = json_decode($this->base64UrlDecode($encodedPayload), true);

        if (! is_array($header) || ! is_array($payload) || ($header['alg'] ?? null) !== 'HS256') {
            throw new RuntimeException('Token payload is invalid.');
        }

        $expected = $this->base64UrlEncode(
            hash_hmac('sha256', "{$encodedHeader}.{$encodedPayload}", $this->secret(), true)
        );

        if (! hash_equals($expected, $encodedSignature)) {
            throw new RuntimeException('Token signature is invalid.');
        }

        return $payload;
    }

    protected function secret(): string
    {
        $secret = (string) config('auth_api.jwt.secret');

        if (strpos($secret, 'base64:') === 0) {
            $decoded = base64_decode(substr($secret, 7), true);

            if ($decoded !== false) {
                return $decoded;
            }
        }

        if ($secret === '') {
            throw new RuntimeException('JWT secret is not configured.');
        }

        return $secret;
    }

    protected function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        if ($decoded === false) {
            throw new RuntimeException('Token encoding is invalid.');
        }

        return $decoded;
    }
}
