<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class VtuRouteAuthTest extends TestCase
{
    public function test_all_vtu_routes_use_the_custom_api_login_jwt_middleware(): void
    {
        $routeNames = [
            'vtu.dashboard',
            'vtu.services',
            'vtu.providers',
            'vtu.products',
            'vtu.validate-customer',
            'vtu.payments.initialize',
            'vtu.purchase',
            'vtu.transactions',
            'vtu.transactions.show',
            'vtu.transactions.refresh',
        ];

        foreach ($routeNames as $routeName) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route, "Route [{$routeName}] is registered.");

            $middleware = $route->gatherMiddleware();

            $this->assertContains('auth.jwt', $middleware, "Route [{$routeName}] must accept /api/auth/login access tokens.");
            $this->assertNotContains('auth:api', $middleware, "Route [{$routeName}] must not use the legacy auth:api guard.");
            $this->assertNotContains('jwt.auth', $middleware, "Route [{$routeName}] must not use the legacy jwt.auth middleware.");
            $this->assertNotContains('auth:sanctum', $middleware, "Route [{$routeName}] must not use Sanctum.");
        }
    }
}
