<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\CustomEloquentUserProvider;
use App\Auth\CustomTokenRepository;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register custom user provider
        Auth::provider('custom-eloquent', function ($app, array $config) {
            return new CustomEloquentUserProvider($app['hash'], $config['model']);
        });

        // Register custom token repository in the service container
        $this->app->singleton('auth.password.tokens', function () {
            return new CustomTokenRepository();
        });
    }
} 