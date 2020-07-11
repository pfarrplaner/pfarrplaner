<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{

    /**
     *
     */
    public const SUPER = 'Super-Administrator*in';
    /**
     *
     */
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
