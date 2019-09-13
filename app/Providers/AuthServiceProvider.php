<?php

namespace App\Providers;

use App\City;
use App\Day;
use App\Location;
use App\Policies\CityPolicy;
use App\Policies\DayPolicy;
use App\Policies\LocationPolicy;
use App\Policies\RolePolicy;
use App\Policies\ServicePolicy;
use App\Policies\UserPolicy;
use App\Service;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Location::class => LocationPolicy::class,
        City::class => CityPolicy::class,
        //Service::class => ServicePolicy::class,
        '\App\Service' => ServicePolicy::class,
        'App\Service' => ServicePolicy::class,
        Day::class => DayPolicy::class,
        Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::before(function($user, $ability) {
           if ($user->hasRole('Super-Administrator*in')) return true;
           if ($user->hasRole('Administrator*in') && ($ability != 'superadmin-bearbeiten') && ($ability != 'admin-bearbeiten')) return true;
        });

        Gate::define('calendar.month', 'App\Policies\CalendarPolicy@month');
        Gate::define('calendar.print', 'App\Policies\CalendarPolicy@print');
        Gate::define('calendar.printsetup', 'App\Policies\CalendarPolicy@printsetup');

    }
}
