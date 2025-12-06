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
        // Use safe helper that handles DB unavailability gracefully
        $appearanceSettings = safe_appearance_settings();
        
        // Share settings with all views (will be null if DB unavailable)
        view()->share('appearanceSettings', $appearanceSettings);

        return $next($request);
    }
} 