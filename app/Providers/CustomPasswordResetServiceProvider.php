<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use App\Auth\CustomPasswordBroker;
use App\Auth\CustomDatabaseTokenRepository;

class CustomPasswordResetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->make('auth.password')->extend('custom', function ($app, $name, $config) {
            $key = $app['config']['app.key'];
            
            if (empty($key)) {
                throw new \RuntimeException('Application key not set.');
            }

            $connection = $config['connection'] ?? null;
            
            $table = $config['table'];
            $hashKey = $config['key'] ?? $key;
            $expire = $config['expire'];
            
            $tokenRepository = new CustomDatabaseTokenRepository(
                $app['db']->connection($connection),
                $app['hash'],
                $table,
                $hashKey,
                $expire
            );

            $users = $app['auth']->createUserProvider($config['provider'] ?? null);
            
            return new CustomPasswordBroker($tokenRepository, $users);
        });
    }
} 