<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Exception;

class DatabaseHelper
{
    /**
     * Execute a database query safely, catching connection errors.
     * Returns null on failure and logs the error.
     *
     * @param callable $callback
     * @param mixed $default Default value to return on error
     * @return mixed
     */
    public static function safeQuery(callable $callback, $default = null)
    {
        try {
            return $callback();
        } catch (Exception $e) {
            Log::warning('Database query failed (DB may be unavailable): ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return $default;
        }
    }

    /**
     * Get Settings model safely.
     *
     * @return \App\Models\Settings|null
     */
    public static function safeSettings()
    {
        return self::safeQuery(function () {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                return \App\Models\Settings::find(1);
            }
            return null;
        });
    }

    /**
     * Get AppearanceSettings model safely.
     *
     * @return \App\Models\AppearanceSettings|null
     */
    public static function safeAppearanceSettings()
    {
        return self::safeQuery(function () {
            if (\Illuminate\Support\Facades\Schema::hasTable('appearance_settings')) {
                return \App\Models\AppearanceSettings::first();
            }
            return null;
        });
    }
}
