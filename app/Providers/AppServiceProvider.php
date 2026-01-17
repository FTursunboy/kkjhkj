<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Регистрация Telegram провайдера для Socialite
        $this->bootTelegramSocialite();
    }

    /**
     * Bootstrap Telegram Socialite provider
     */
    protected function bootTelegramSocialite(): void
    {
        $socialite = $this->app->make(SocialiteFactory::class);
        
        $socialite->extend('telegram', function () use ($socialite) {
            $config = config('services.telegram');
            return $socialite->buildProvider(
                \SocialiteProviders\Telegram\Provider::class,
                $config
            );
        });
    }
}
