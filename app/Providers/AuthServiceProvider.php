<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];
///////////
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('view-payments', function ($user) {

            return in_array($user->role ?? null, ['admin', 'tutor']);
        });
    }
    /////////
}
