<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuthOtp;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\JwtService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected JwtService $jwt;
    protected OtpService $otp;

    public function __construct(JwtService $jwt, OtpService $otp)
    {
        $this->jwt = $jwt;
        $this->otp = $otp;
    }

    public function signup(Request $request): JsonResponse
    {
        $this->normalizeRequestContacts($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'verification_channel' => ['nullable', Rule::in([AuthOtp::CHANNEL_EMAIL, AuthOtp::CHANNEL_PHONE])],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'user_type' => config('auth_api.defaults.user_type', 'user'),
            'country' => $data['country_id'] ?? config('auth_api.defaults.country_id', 1),
        ]);

        [$channel, $identifier] = $this->verificationTarget($user, $data['verification_channel'] ?? null);
        $code = $this->otp->create($user, AuthOtp::PURPOSE_SIGNUP, $channel, $identifier);

        return response()->json([
            'status' => true,
            'message' => 'Account created. Verify the OTP to activate the account.',
            'data' => array_merge([
                'user' => $this->userPayload($user),
                'verification_channel' => $channel,
                'verification_identifier' => $identifier,
            ], $this->debugOtp($code)),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $this->normalizeRequestContacts($request);

        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $user = $this->findUserByIdentifier($data['identifier']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login credentials.',
            ], 401);
        }

        if (! $user->hasVerifiedContact()) {
            return response()->json([
                'status' => false,
                'message' => 'OTP verification is required before login.',
                'data' => [
                    'verification_identifier' => $this->preferredIdentifier($user),
                ],
            ], 403);
        }

        return $this->tokenResponse($user, 'Login successful.');
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $this->normalizeRequestContacts($request);

        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'otp' => ['required', 'digits:6'],
        ]);

        $otp = $this->otp->verify($data['identifier'], AuthOtp::PURPOSE_SIGNUP, $data['otp']);
        $user = $otp->user;

        if ($otp->channel === AuthOtp::CHANNEL_EMAIL) {
            $user->forceFill(['email_verified_at' => $user->email_verified_at ?: now()])->save();
        }

        if ($otp->channel === AuthOtp::CHANNEL_PHONE) {
            $user->forceFill(['phone_verified_at' => $user->phone_verified_at ?: now()])->save();
        }

        return $this->tokenResponse($user->fresh(), 'OTP verified successfully.');
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $this->normalizeRequestContacts($request);

        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'purpose' => ['nullable', Rule::in([AuthOtp::PURPOSE_SIGNUP, AuthOtp::PURPOSE_PASSWORD_RESET])],
        ]);

        $purpose = $data['purpose'] ?? AuthOtp::PURPOSE_SIGNUP;
        $user = $this->findUserByIdentifier($data['identifier']);

        if (! $user) {
            throw ValidationException::withMessages([
                'identifier' => ['No account was found for this email or phone.'],
            ]);
        }

        if ($purpose === AuthOtp::PURPOSE_SIGNUP && $user->hasVerifiedContact()) {
            throw ValidationException::withMessages([
                'identifier' => ['This account has already been verified.'],
            ]);
        }

        [$channel, $identifier] = $this->verificationTarget($user);
        $code = $this->otp->create($user, $purpose, $channel, $identifier);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully.',
            'data' => array_merge([
                'verification_channel' => $channel,
                'verification_identifier' => $identifier,
                'purpose' => $purpose,
            ], $this->debugOtp($code)),
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $this->normalizeRequestContacts($request);

        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
        ]);

        $user = $this->findUserByIdentifier($data['identifier']);
        $debug = [];

        if ($user) {
            [$channel, $identifier] = $this->verificationTarget($user);
            $code = $this->otp->create($user, AuthOtp::PURPOSE_PASSWORD_RESET, $channel, $identifier);
            $debug = $this->debugOtp($code);
        }

        return response()->json([
            'status' => true,
            'message' => 'If the account exists, a password reset OTP has been sent.',
            'data' => $debug,
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $this->normalizeRequestContacts($request);

        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $this->findUserByIdentifier($data['identifier']);

        if (! $user) {
            throw ValidationException::withMessages([
                'identifier' => ['No account was found for this email or phone.'],
            ]);
        }

        $this->otp->verify($data['identifier'], AuthOtp::PURPOSE_PASSWORD_RESET, $data['otp']);

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        $this->jwt->revokeAllForUser($user);

        return $this->tokenResponse($user->fresh(), 'Password reset successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [
                'user' => $this->userPayload($request->user()),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->attributes->get('jwt_token');

        if ($token) {
            $this->jwt->revokeToken($token);
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'The password is incorrect.',
            ], 422);
        }

        $token = $request->attributes->get('jwt_token');
        if ($token) {
            $this->jwt->revokeToken($token);
        }

        try {
            $user->delete();
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not delete your account. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Your account has been deleted successfully.',
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $token = $request->attributes->get('jwt_token');
        $user = $request->user();

        if ($token) {
            $this->jwt->revokeToken($token);
        }

        return $this->tokenResponse($user, 'Token refreshed successfully.');
    }

    protected function tokenResponse(User $user, string $message): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => [
                'user' => $this->userPayload($user),
                'token' => $this->jwt->issueToken($user),
            ],
        ]);
    }

    protected function userPayload(User $user): array
    {
        $user->loadMissing('activeVehicles.shippingCost');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'email_verified_at' => optional($user->email_verified_at)->toIso8601String(),
            'phone_verified_at' => optional($user->phone_verified_at)->toIso8601String(),
            'is_verified' => $user->hasVerifiedContact(),
            'vehicles' => in_array($user->user_type, ['dispatcher', 'courier'], true)
                ? $user->activeVehicles->map(function (Vehicle $vehicle) {
                    return [
                        'id' => $vehicle->id,
                        'type' => $vehicle->type,
                        'brand' => $vehicle->brand,
                        'model' => $vehicle->model,
                        'plate_number' => $vehicle->plate_number,
                        'color' => $vehicle->color,
                        'year' => $vehicle->year,
                        'status' => $vehicle->status,
                        'shipping_rate' => $vehicle->shippingCost ? [
                            'id' => $vehicle->shippingCost->id,
                            'label' => $vehicle->shippingCost->display_label,
                            'weight' => $vehicle->shippingCost->weight,
                            'country_name' => $vehicle->shippingCost->country_name,
                            'country_iso_2' => $vehicle->shippingCost->country_iso_2,
                            'cost' => $vehicle->shippingCost->cost,
                        ] : null,
                    ];
                })->values()
                : [],
        ];
    }

    protected function findUserByIdentifier(string $identifier): ?User
    {
        $identifier = $this->otp->normalizeIdentifier($identifier);

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', $identifier)->first();
        }

        return User::where('phone', $identifier)->first();
    }

    protected function verificationTarget(User $user, ?string $preferredChannel = null): array
    {
        $channel = $preferredChannel ?: ($user->email ? AuthOtp::CHANNEL_EMAIL : AuthOtp::CHANNEL_PHONE);

        if ($channel === AuthOtp::CHANNEL_EMAIL && $user->email) {
            return [$channel, $user->email];
        }

        if ($channel === AuthOtp::CHANNEL_PHONE && $user->phone) {
            return [$channel, $user->phone];
        }

        throw ValidationException::withMessages([
            'verification_channel' => ['The requested verification channel is not available for this account.'],
        ]);
    }

    protected function preferredIdentifier(User $user): ?string
    {
        return $user->email ?: $user->phone;
    }

    protected function normalizeRequestContacts(Request $request): void
    {
        $updates = [];

        foreach (['email', 'phone', 'identifier'] as $field) {
            if ($request->filled($field)) {
                $updates[$field] = $this->otp->normalizeIdentifier((string) $request->input($field));
            }
        }

        if ($updates) {
            $request->merge($updates);
        }
    }

    protected function debugOtp(?string $code): array
    {
        if (! $code || app()->environment('production') || ! config('auth_api.otp.expose_debug_otp')) {
            return [];
        }

        return ['debug_otp' => $code];
    }
}
