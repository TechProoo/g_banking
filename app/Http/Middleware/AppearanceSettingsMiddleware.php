<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AppearanceSettings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AppearanceSettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!Schema::hasTable('appearance_settings')) {
                return $next($request);
            }

            // Get appearance settings
            $appearanceSettings = AppearanceSettings::first();

            // Share settings with all views
            view()->share('appearanceSettings', $appearanceSettings);

        } catch (\Exception $e) {
            // Log and allow request to continue when DB is unavailable
            Log::warning('AppearanceSettingsMiddleware skipped: ' . $e->getMessage());
            return $next($request);
        }

        return $next($request);
    }
} 