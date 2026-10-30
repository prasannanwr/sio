<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthenticateJwt
{
    protected JwtService $jwt;

    public function __construct(JwtService $jwt)
    {
        $this->jwt = $jwt;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication token is required.',
            ], 401);
        }

        try {
            [$user, $tokenRecord, $payload] = $this->jwt->authenticate($token);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);
        $request->attributes->set('jwt_token', $tokenRecord);
        $request->attributes->set('jwt_payload', $payload);

        return $next($request);
    }
}
