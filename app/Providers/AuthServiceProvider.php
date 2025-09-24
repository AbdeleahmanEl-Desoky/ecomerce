<?php

namespace App\Providers;

use App\Policies\UserPolicy;
use App\Providers\CustomUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\User\Models\User;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register custom user providers for role-based authentication
        Auth::provider('admin_users', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model'], 'admin');
        });

        Auth::provider('customer_users', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model'], 'customer');
        });

        // Define gates for role-based access
        Gate::define('admin-access', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('customer-access', function (User $user) {
            return $user->role === 'customer';
        });

    }
}
