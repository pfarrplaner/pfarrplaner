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

namespace App\Policies;

use App\User;
use App\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class LocationPolicy
 * @package App\Policies
 */
class LocationPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function index(User $user) {
        if ($user->hasRole('Administrator*in')) return true;
        if ($user->can('ort-bearbeiten') || $user->can('gd-opfer-bearbeiten')) return true;
        return false;
    }

    /**
     * Determine whether the user can view the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function view(User $user, Location $location)
    {
        return true;
    }

    /**
     * Determine whether the user can create locations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can update the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function update(User $user, Location $location)
    {
        if ($user->hasRole('Administrator*in') && $location->city->administeredBy($user)) return true;
        if ($user->can('kirche-bearbeiten') && $user->writableCities->contains($location->city)) return true;
        return false;
    }

    /**
     * Determine whether the user can delete the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function delete(User $user, Location $location)
    {
        if ($user->hasRole('Administrator*in') && $location->city->administeredBy($user)) return true;
        if ($user->can('kirche-bearbeiten') && $user->writableCities->contains($location->city)) return true;
        return false;
    }

    /**
     * Determine whether the user can restore the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function restore(User $user, Location $location)
    {
        if ($user->hasRole('Administrator*in') && $location->city->administeredBy($user)) return true;
        if ($user->can('kirche-bearbeiten') && $user->writableCities->contains($location->city)) return true;
        return false;
    }

    /**
     * Determine whether the user can permanently delete the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function forceDelete(User $user, Location $location)
    {
        if ($user->hasRole('Administrator*in') && $location->city->administeredBy($user)) return true;
        if ($user->can('kirche-bearbeiten') && $user->writableCities->contains($location->city)) return true;
        return false;
    }
}
