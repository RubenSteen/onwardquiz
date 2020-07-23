<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // Gate::define('viewAny-map', 'App\Policies\MapPolicy@viewAny');
        // Gate::define('view-map', 'App\Policies\MapPolicy@view');
        // Gate::define('create-map', 'App\Policies\MapPolicy@create');
        // Gate::define('update-map', 'App\Policies\MapPolicy@update');
        // Gate::define('delete-map', 'App\Policies\MapPolicy@delete');
        // Gate::define('forceDelete-map', 'App\Policies\MapPolicy@forceDelete');
        // Gate::define('restore-map', 'App\Policies\MapPolicy@restore');

        Gate::define('index-user', 'App\Policies\UserPolicy@index');
        Gate::define('viewAny-user', 'App\Policies\UserPolicy@viewAny');
        Gate::define('view-user', 'App\Policies\UserPolicy@view');
        Gate::define('update-user', 'App\Policies\UserPolicy@update');
        Gate::define('delete-user', 'App\Policies\UserPolicy@delete');
        Gate::define('restore-user', 'App\Policies\UserPolicy@restore');
        Gate::define('forceDelete-user', 'App\Policies\UserPolicy@forceDelete');
        Gate::define('confirm-user', 'App\Policies\UserPolicy@confirm');
        Gate::define('ban-user', 'App\Policies\UserPolicy@ban');
    }
}
