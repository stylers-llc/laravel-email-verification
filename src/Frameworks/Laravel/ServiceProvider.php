<?php

namespace Stylers\EmailVerification\Frameworks\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Stylers\EmailVerification\EmailVerificationServiceInterface;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/_publish/database/migrations' => database_path('migrations')
        ], 'migrations');
        $this->publishes([
            __DIR__.'/_publish/config' => config_path()
        ], 'config');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EmailVerificationServiceInterface::class, EmailVerificationService::class);
    }
}
