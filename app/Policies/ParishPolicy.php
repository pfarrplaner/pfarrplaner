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

use App\Parish;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ParishPolicy
 * @package App\Policies
 */
class ParishPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param User $user
     * @return bool
     */
    public function index(User $user)
    {
        if ($user->hasRole('Administrator*in')) {
            return true;
        }
        if ($user->isLocalAdmin) return true;
        return $user->hasPermissionTo('pfarramt-bearbeiten');
    }

    /**
     * @param User $user
     * @param Parish $model
     * @return bool
     */
    public function create(User $user, Parish $model)
    {
        if ($user->adminCities->contains($model->owningCity)) {
            return true;
        }
        return $user->hasPermissionTo('pfarramt-bearbeiten');
    }

    /**
     * @param User $user
     * @param Parish $model
     * @return bool
     */
    public function update(User $user, Parish $model)
    {
        if ($user->adminCities->contains($model->owningCity)) {
            return true;
        }
        return $user->hasPermissionTo('pfarramt-bearbeiten');
    }

    /**
     * @param User $user
     * @param Parish $model
     * @return bool
     */
    public function delete(User $user, Parish $model)
    {
        if ($user->adminCities->contains($model->owningCity)) {
            return true;
        }
        return $user->hasPermissionTo('pfarramt-bearbeiten');
    }

    /**
     * @param User $user
     * @param Parish $model
     * @return bool
     */
    public function restore(User $user, Parish $model)
    {
        return $user->hasPermissionTo('pfarramt-bearbeiten');
    }

    /**
     * @param User $user
     * @param Parish $model
     * @return bool
     */
    public function forceDelete(User $user, Parish $model)
    {
        return $user->hasPermissionTo('pfarramt-bearbeiten');
    }
}
