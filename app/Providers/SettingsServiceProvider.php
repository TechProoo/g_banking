<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Settings;
use App\Models\Paystack;
use App\Models\SettingsCont;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Avoid querying the database when running in console (migrations, composer, artisan)
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            // If the settings table doesn't exist yet (fresh install/migration pending), skip
            if (!Schema::hasTable('settings')) {
                return;
            }

            $settings = Settings::find(1);
            $paystack = Paystack::find(1);
            $settings2 = SettingsCont::find(1);

            if (!$settings) {
                return;
            }

            $assetUrl = null;
            if (isset($settings->install_type) && $settings->install_type == 'Sub-Folder') {
                $urls = explode('/', $settings->site_address);
                $assetUrl = '/' . end($urls);
            }

            // Set configuration values at run time (use null coalescing to avoid errors)
            config([
                'captcha.secret' => $settings->capt_secret ?? null,
                'captcha.sitekey' => $settings->capt_sitekey ?? null,
                'services.google.client_id' =>  $settings->google_id ?? null,
                'services.google.client_secret' =>  $settings->google_secret ?? null,
                'services.google.redirect' =>  $settings->google_redirect ?? null,
                'mail.mailers.smtp.host' =>  $settings->smtp_host ?? null,
                'mail.mailers.smtp.port' =>  $settings->smtp_port ?? null,
                'mail.mailers.smtp.encryption' =>  $settings->smtp_encrypt ?? null,
                'mail.mailers.smtp.username' =>  $settings->smtp_user ?? null,
                'mail.mailers.smtp.password' =>  $settings->smtp_password ?? null,
                'mail.default' => $settings->mail_server ?? null,
                'mail.from.address' => $settings->emailfrom ?? null,
                'mail.from.name' => $settings->emailfromname ?? null,
                'app.timezone' => $settings->timezone ?? config('app.timezone'),
                'app.name' => $settings->site_name ?? config('app.name'),
                'app.url' => $settings->site_address ?? config('app.url'),
                'paystack.publicKey' => $paystack->paystack_public_key ?? null,
                'paystack.secretKey' => $paystack->paystack_secret_key ?? null,
                'paystack.paymentUrl' => $paystack->paystack_url ?? null,
                'paystack.merchantEmail' => $paystack->paystack_email ?? null,
                'livewire.asset_url' => $assetUrl,
                'flutterwave.publicKey' => $settings2->flw_public_key ?? null,
                'flutterwave.secretKey' => $settings2->flw_secret_key ?? null,
                'flutterwave.secretHash' => $settings2->flw_secret_hash ?? null,
                'services.telegram-bot-api.token' =>  $settings2->telegram_bot_api ?? null,
            ]);

        } catch (\Exception $e) {
            // Log and continue — do not break the application when DB is unavailable
            Log::warning('SettingsServiceProvider boot skipped: ' . $e->getMessage());
            return;
        }
    }
}