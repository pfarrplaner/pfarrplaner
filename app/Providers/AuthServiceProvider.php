<?php

namespace App\Providers;

use App\Absence;
use App\City;
use App\Day;
use App\Location;
use App\Policies\AbsencePolicy;
use App\Policies\CityPolicy;
use App\Policies\DayPolicy;
use App\Policies\LocationPolicy;
use App\Policies\ParishPolicy;
use App\Policies\RolePolicy;
use App\Policies\ServicePolicy;
use App\Policies\TagPolicy;
use App\Policies\UserPolicy;
use App\Service;
use App\Tag;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{

    public const SUPER = 'Super-Administrator*in';
    public const ADMIN = 'Administrator*in';

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
        Absence::class => AbsencePolicy::class,
        Parish::class => ParishPolicy::class,
        Tag::class => TagPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super-Administrator*in can do everything
        Gate::before(function($user, $ability) {
           if ($user->hasRole(self::SUPER)) return true;
        });

        Gate::define('calendar.month', 'App\Policies\CalendarPolicy@month');
        Gate::define('calendar.print', 'App\Policies\CalendarPolicy@print');
        Gate::define('calendar.printsetup', 'App\Policies\CalendarPolicy@printsetup');

    }
}
