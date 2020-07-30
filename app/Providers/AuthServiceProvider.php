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
//        'App\Model' => 'App\Policies\ModelPolicy'
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

         Gate::define('index-map', 'App\Policies\MapPolicy@index');
         Gate::define('create-map', 'App\Policies\MapPolicy@create');
         Gate::define('viewAny-map', 'App\Policies\MapPolicy@viewAny');
         Gate::define('view-map', 'App\Policies\MapPolicy@view');
         Gate::define('update-map', 'App\Policies\MapPolicy@update');
         Gate::define('delete-map', 'App\Policies\MapPolicy@delete');
         Gate::define('forceDelete-map', 'App\Policies\MapPolicy@forceDelete');
         Gate::define('restore-map', 'App\Policies\MapPolicy@restore');
    }
}
