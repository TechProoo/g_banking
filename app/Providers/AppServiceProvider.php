<?php

namespace App\Providers;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use Illuminate\Support\Facades\View;
use App\Models\Settings;
use App\Models\SettingsCont;
use App\Models\TermsPrivacy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        FacadesStorage::extend('sftp', function ($app, $config) {
            return new Filesystem(new SftpAdapter($config));
        });

        Paginator::useBootstrap();

        // Sharing settings with all views — guard against DB unavailability
        if ($this->app->runningInConsole()) {
            return;
        }

        // Ensure session middleware doesn't try to read DB sessions when DB is down
        try {
            if (!Schema::hasTable('sessions')) {
                config(['session.driver' => 'file']);
                Log::warning('Session table not found — falling back to file session driver.');
            }
        } catch (\Exception $e) {
            // If any DB error occurs, fallback to file sessions so middleware won't crash
            config(['session.driver' => 'file']);
            Log::warning('Could not check sessions table — using file session driver: ' . $e->getMessage());
        }

        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            $settings = Settings::find(1);
            $terms = TermsPrivacy::find(1);
            $moreset = SettingsCont::find(1);

            View::share('settings', $settings);
            View::share('terms', $terms);
            View::share('moresettings', $moreset);
            View::share('mod', $settings->modules ?? null);

        } catch (\Exception $e) {
            Log::warning('AppServiceProvider boot skipped: ' . $e->getMessage());
            return;
        }
    }
}