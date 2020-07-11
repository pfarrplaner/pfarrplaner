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

use App\Providers\AuthServiceProvider;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class UserPolicy
 * @package App\Policies
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function index(User $user) {
        if ($user->hasRole('Administrator*in')) return true;
        return $user->hasPermissionTo('benutzerliste-lokal-sehen') || $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if (count($user->adminCities)) return true;
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        if ($model->hasRole(AuthServiceProvider::SUPER)) return false;
        if ($user->hasRole(AuthServiceProvider::ADMIN)) return true;
        if (count($user->adminCities->intersect($model->cities))) return true;
        if ($user->hasPermissionTo('benutzer-bearbeiten')) {
            foreach ($model->homeCities as $city) {
                if ($city->administeredBy($user)) return true;
            }
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        if ($model->hasRole(AuthServiceProvider::SUPER)) return false;
        if ($user->hasRole(AuthServiceProvider::ADMIN) || $user->hasPermissionTo('benutzer-bearbeiten')) {
            foreach ($model->homeCities as $city) {
                if ($city->administeredBy($user)) return true;
            }
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can choose another user to merge the two
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function join(User $user, User $model) {
        if ($user->hasRole(AuthServiceProvider::ADMIN)) return true;
        if ($user->hasPermissionTo('benutzer-bearbeiten')) return true;
    }

    /**
     * Determine whether the user can merge this user into another one
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function doJoin(User $user, User $model) {
        return true;
    }


    /**
     * Determine wether the user can edit another users absences
     * @param User $user
     * @param User $model
     */
    public function editAbsences (User $user, User $model) {
        if ($user->id == $model->id) return true;
        if ($user->hasPermissionTo('fremden-urlaub-bearbeiten') || ($user->hasRole(AuthServiceProvider::ADMIN))) {
            if (!$model->hasRole('Pfarrer*in')) {
                if (count($user->writableCities->intersect($model->homeCities))) return true;
            }
        }
        return false;
    }

}

