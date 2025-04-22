<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Grant all permissions to super-admin role
        Gate::before(function ($user, $ability) {
            return $user->hasRole(config('permission.super-admin')) ? true : null;
        });

        // Define gates for users
        Gate::define('view_users', fn($user) => $user->hasPermissionTo('view users'));
        Gate::define('create_users', fn($user) => $user->hasPermissionTo('create users'));
        Gate::define('edit_users', fn($user) => $user->hasPermissionTo('edit users'));
        Gate::define('delete_users', fn($user) => $user->hasPermissionTo('delete users'));

        // Define gates for roles
        Gate::define('view_roles', fn($user) => $user->hasPermissionTo('view roles'));
        Gate::define('create_roles', fn($user) => $user->hasPermissionTo('create roles'));
        Gate::define('edit_roles', fn($user) => $user->hasPermissionTo('edit roles'));
        Gate::define('delete_roles', fn($user) => $user->hasPermissionTo('delete roles'));

        // Define gates for invoices
        Gate::define('view_invoices', fn($user) => $user->hasPermissionTo('view invoices'));
        Gate::define('create_invoices', fn($user) => $user->hasPermissionTo('create invoices'));
        Gate::define('edit_invoices', fn($user) => $user->hasPermissionTo('edit invoices'));
        Gate::define('delete_invoices', fn($user) => $user->hasPermissionTo('delete invoices'));

        // Define gates for quotes
        Gate::define('view_quotes', fn($user) => $user->hasPermissionTo('view quotes'));
        Gate::define('create_quotes', fn($user) => $user->hasPermissionTo('create quotes'));
        Gate::define('edit_quotes', fn($user) => $user->hasPermissionTo('edit quotes'));
        Gate::define('delete_quotes', fn($user) => $user->hasPermissionTo('delete quotes'));
    }

    public function createProvider($provider)
    {
        return new $provider($this->app);
    }
}