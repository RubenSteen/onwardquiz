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

        Gate::define('viewAny-map', 'App\Policies\MapPolicy@viewAny');
        Gate::define('create-map', 'App\Policies\MapPolicy@create');
        Gate::define('update-map', 'App\Policies\MapPolicy@update');
        Gate::define('delete-map', 'App\Policies\MapPolicy@delete');
        Gate::define('forceDelete-map', 'App\Policies\MapPolicy@forceDelete');
        Gate::define('restore-map', 'App\Policies\MapPolicy@restore');

        Gate::define('create-question', 'App\Policies\QuestionPolicy@create');
        Gate::define('update-question', 'App\Policies\QuestionPolicy@update');
        Gate::define('delete-question', 'App\Policies\QuestionPolicy@delete');
        Gate::define('restore-question', 'App\Policies\QuestionPolicy@restore');
        Gate::define('forceDelete-question', 'App\Policies\QuestionPolicy@forceDelete');
        Gate::define('create-question-picture', 'App\Policies\QuestionPolicy@createPicture');
        Gate::define('update-question-picture', 'App\Policies\QuestionPolicy@updatePicture');
        Gate::define('delete-question-picture', 'App\Policies\QuestionPolicy@deleteDelete');
        Gate::define('restore-question-picture', 'App\Policies\QuestionPolicy@restorePicture');
        Gate::define('forceDelete-question-picture', 'App\Policies\QuestionPolicy@forceDeletePicture');
    }
}
