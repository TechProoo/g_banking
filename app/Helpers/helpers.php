<?php

use App\Helpers\DatabaseHelper;

if (!function_exists('safe_query')) {
    /**
     * Execute a database query safely, returning default on error.
     *
     * @param callable $callback
     * @param mixed $default
     * @return mixed
     */
    function safe_query(callable $callback, $default = null)
    {
        return DatabaseHelper::safeQuery($callback, $default);
    }
}

if (!function_exists('safe_settings')) {
    /**
     * Get Settings model safely.
     *
     * @return \App\Models\Settings|null
     */
    function safe_settings()
    {
        return DatabaseHelper::safeSettings();
    }
}

if (!function_exists('safe_appearance_settings')) {
    /**
     * Get AppearanceSettings model safely.
     *
     * @return \App\Models\AppearanceSettings|null
     */
    function safe_appearance_settings()
    {
        return DatabaseHelper::safeAppearanceSettings();
    }
}
