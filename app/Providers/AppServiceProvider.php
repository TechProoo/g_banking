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
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }
        
        // Always force HTTPS for assets if request is secure
        if (request()->isSecure()) {
            \URL::forceScheme('https');
        }

        FacadesStorage::extend('sftp', function ($app, $config) {
            return new Filesystem(new SftpAdapter($config));
        });

        Paginator::useBootstrap();

        // Ensure necessary storage directories exist to avoid filesystem errors
        $storageDirs = [
            \storage_path('framework/sessions'),
            \storage_path('framework/views'),
            \storage_path('framework/cache'),
            \storage_path('framework/cache/data'),
            \storage_path('logs'),
            \storage_path('app'),
        ];

        // compute bootstrap cache dir without calling bootstrap_path() which may not be available yet
        $projectRoot = dirname(__DIR__, 3);
        $bootstrapCache = $projectRoot . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache';
        $storageDirs[] = $bootstrapCache;

        foreach ($storageDirs as $dir) {
            try {
                if (!\is_dir($dir)) {
                    @mkdir($dir, 0777, true);
                }
                @chmod($dir, 0777);
            } catch (\Exception $e) {
                Log::warning('Could not create storage dir ' . $dir . ' : ' . $e->getMessage());
            }
        }

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