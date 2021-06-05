<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Policies;

use App\Providers\AuthServiceProvider;
use App\Service;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ServicePolicy
 * @package App\Policies
 */
class ServicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the service.
     *
     * @param User $user
     * @param Service $service
     * @return mixed
     */
    public function view(User $user, Service $service)
    {
        return true;
    }

    /**
     * Determine whether the user can create services.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('gd-bearbeiten');
    }

    /**
     * Determine whether the user can update the service.
     *
     * @param User $user
     * @param Service $service
     * @return mixed
     */
    public function update(User $user, Service $service)
    {
        if ($service->pastors->contains($user)) return true;
        if ($user->hasRole(AuthServiceProvider::ADMIN) && $this->hasCityPermission($user, $service)) {
            return true;
        }
        if ($user->hasPermissionTo('gd-bearbeiten') && $this->hasCityPermission($user, $service)) {
            return true;
        }
        return false;
    }

    /**
     * @param $user
     * @param $service
     * @return mixed
     */
    protected function hasCityPermission($user, $service)
    {
        return $user->writableCities->pluck('id')->contains($service->city_id);
    }

    /**
     * Determine whether the user can delete the service.
     *
     * @param User $user
     * @param Service $service
     * @return mixed
     */
    public function delete(User $user, Service $service)
    {
        return $user->hasPermissionTo('gd-allgemein-bearbeiten') && $this->hasCityPermission($user, $service);
    }

    /**
     * Determine whether the user can restore the service.
     *
     * @param User $user
     * @param Service $service
     * @return mixed
     */
    public function restore(User $user, Service $service)
    {
        return $user->hasPermissionTo('gd-allgemein-bearbeiten') && $this->hasCityPermission($user, $service);
    }

    /**
     * Determine whether the user can permanently delete the service.
     *
     * @param User $user
     * @param Service $service
     * @return mixed
     */
    public function forceDelete(User $user, Service $service)
    {
        return $user->hasPermissionTo('gd-allgemein-bearbeiten') && $this->hasCityPermission($user, $service);
    }
}
