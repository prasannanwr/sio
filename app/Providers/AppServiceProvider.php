<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {   
        
            $this->app->bind('path.public', function() {
                return base_path('../public_html');
            });
        
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // Ensure floats serialize to their shortest round-trip form in JSON (e.g. 2530.49 instead of
        // 2530.4899999999998). Some environments set this high, which breaks gateways like Monnify
        // that expect at most 2 decimal places, and produces ugly amounts in API responses.
        ini_set('serialize_precision', '-1');

        // Apply the admin-managed Monnify credentials (overrides config/.env) when configured.
        // Wrapped so a missing table during migrate/install never breaks the app.
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('monnify_configurations')) {
                \App\Models\MonnifyConfiguration::applyActiveIfAny();
            }
        } catch (\Throwable $e) {
            // ignore — fall back to config/.env
        }
    }
}
