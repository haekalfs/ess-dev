<?php

namespace App\Providers;

use App\Models\User_access;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('role', function ($role) {
            return in_array($role, session('allowed_roles', []));
        });

        Blade::if('usr_acc', function ($usr_acc) {
            $allowedRole = User_access::where('page_id', $usr_acc)
                ->join('roles', 'user_access.role_id', '=', 'roles.id')
                ->pluck('roles.role')
                ->toArray();
            $allowedRolesInSession = session('allowed_roles', []);
            return array_intersect($allowedRole, $allowedRolesInSession);
        });
    }
}
