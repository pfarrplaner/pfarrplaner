<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
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

namespace App\UI\Modules;

use App\City;
use App\Inputs\AbstractInput;
use App\Inputs\Inputs;
use App\Location;
use App\Parish;
use App\Tag;
use App\Team;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

class AdminModule extends AbstractModule
{
    protected $title = 'Administration';
    protected $icon = 'user-shield';

    public function addItems(array $items): array
    {
        $adminMenu = [];
        $adminActive = false;
        $user = Auth::user();
        $route = Route::currentRouteName();

        if ($user->can('index', User::class)) {
            $adminMenu[] = [
                'text' => 'Benutzer',
                'icon' => 'fa fa-users',
                'url' => route('users.index'),
                'active' => $route == 'users.index',
                'inertia' => true,
            ];
            $adminActive |= ($route == 'users.index');
        }
        if ($user->can('index', Team::class)) {
            $adminMenu[] = [
                'text' => 'Teams',
                'icon' => 'fa fa-users',
                'url' => route('teams.index'),
                'active' => $route == 'teams.index',
                'inertia' => true,
            ];
            $adminActive |= ($route == 'users.index');
        }
        if ($user->can('index', Role::class)) {
            $adminMenu[] = [
                'text' => 'Benutzerrollen',
                'icon' => 'fa fa-user-tag',
                'url' => route('roles.index'),
                'active' => $route == 'roles.index',
                'inertia' => false,
            ];
            $adminActive |= ($route == 'roles.index');
        }
        if ($user->can('index', City::class)) {
            $adminMenu[] = [
                'text' => 'Kirchengemeinden',
                'icon' => 'fa fa-church',
                'url' => route('cities.index'),
                'active' => $route == 'cities.index',
                'inertia' => true,
            ];
            $adminActive |= ($route == 'cities.index');
        }
        if ($user->can('index', Location::class)) {
            $adminMenu[] = [
                'text' => 'Kirche / GD-Orte',
                'icon' => 'fa fa-map-marker',
                'url' => route('locations.index'),
                'active' => $route == 'locations.index',
                'inertia' => false,
            ];
            $adminActive |= ($route == 'locations.index');
        }
        if ($user->can('index', Tag::class)) {
            $adminMenu[] = [
                'text' => 'Kennzeichnungen',
                'icon' => 'fa fa-tag',
                'url' => route('tags.index'),
                'active' => $route == 'tags.index',
                'inertia' => false,
            ];
            $adminActive |= ($route == 'tags.index');
        }
        if ($user->can('index', Parish::class)) {
            $adminMenu[] = [
                'text' => 'Pfarrämter',
                'icon' => 'fa fa-building',
                'url' => route('parishes.index'),
                'active' => $route == 'parishes.index',
                'inertia' => false,
            ];
            $adminActive |= ($route == 'parishes.index');
        }
        if (count($adminMenu)) {
            $items[] = [
                'text' => 'Administration',
                'icon' => 'fa fa-user-shield',
                'url' => '#',
                'submenu' => $adminMenu,
                'active' => $adminActive,
                'inertia' => false,
            ];
        }
        return $items;
    }

}
